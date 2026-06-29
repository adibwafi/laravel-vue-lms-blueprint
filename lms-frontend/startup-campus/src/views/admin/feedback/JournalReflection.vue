<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div>
    <Header />
    <div class="content-main-area">
      <Loading :isLoading="actLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <div class="k-wrapping">
        <div class="k-dashboard">
          <Sidebar />
          <div class="k-content">
            <div class="k-card">
              <div class="breadcrumbs">
                <router-link
                  :to="`/admin/tracking/feedback?categories_id=${dataParent.categories_id}`"
                  >{{ dataParent.name }}</router-link
                >
                <div class="active">Lesson</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Lesson</h2>
              </div>
              <v-card>
                <v-card-title>
                  <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                  ></v-text-field>
                </v-card-title>
                <v-data-table
                  :headers="headers"
                  :items="datarow"
                  :loading="isLoading"
                  loading-text="Loading... Please wait"
                  :options.sync="options"
                  :server-items-length="totalData"
                >
                  <template v-slot:[`item.name`]="{ item }">
                    <div>
                      <span class="pr-4">{{ item.name }}</span>
                      <span class="pr-4">|</span>
                      <router-link
                        :to="`/admin/tracking/feedback/${course_id}/journal-reflection/${item.id}`"
                        >Lihat Data</router-link
                      >
                    </div>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.deskripsi {
  .label {
    font-weight: 500;
  }

  .informasi {
    font-size: 0.8rem;
    font-weight: 400;
    margin-top: -10px;
  }
}

.using-zoom-group {
  display: flex;
  flex-direction: column;
}

.waktu-zoom {
  margin-top: 1rem;
  font-size: 1rem;
  font-weight: 500;
}

.quiz-form {
  margin-left: 1rem;
}
</style>

<script>
import Header from "@/components/HeaderCMS";
import Sidebar from "@/components/SidebarCMS";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
export default {
  name: "JournalReflectionPage",
  metaInfo() {
    return {
      title: "Journal Reflection  - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {
    Header,
    Sidebar,
    Loading,
    Alert,
  },
  data() {
    return {
      isLoading: false,
      course_id: "",
      id: "",
      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,

      // table
      search: "",
      datarow: [],
      totalData: 0,
      options: {},
      typingTimer: 0,
      selectedItem: {},
      dataParent: "",

      headers: [
        {
          text: "Name",
          value: "name",
          align: "start",
          width: "30%",
        },
        {
          text: "Action",
          value: "action",
          width: "25%",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getMainData();
      },
      deep: true,
    },
    search: {
      handler() {
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => {
          this.getMainData();
        }, 1000);
      },
      deep: true,
    },
  },
  methods: {
    getParentData(id, call_after = null) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course/read/${id}`,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.dataParent = res.data.data.Course;
            return;
          } else {
            // this.$router.push({
            //   path: `/admin/course`,
            // });
          }
          if (call_after != null) {
            call_after();
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0] ? this.options.sortBy[0] : "sorter";
      var sortDesc = this.options.sortDesc[0]
        ? this.options.sortDesc[0]
        : "ASC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/lesson?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&courses_id=${this.$route.params.course_id}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.Lessons.rows;
            this.totalData = res.data.data.Lessons.count;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    alert(item, group) {
      if (group == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = DOMPurify.sanitize(item); // Membersihkan nilai
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
  mounted() {
    var course_id = this.$route.params.course_id;
    if (course_id) {
      this.course_id = course_id;
      this.getParentData(course_id, this.getMainData);
    }
    this.$root.cms_tab = "tracking/feedback";
  },
};
</script>
