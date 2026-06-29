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
                <router-link to="/admin/tracking/course-category"
                  >Course Category</router-link
                >
                <router-link
                  :to="`/admin/tracking/course-category/${courseCategoryId}/course`"
                  >Course</router-link
                >
                <div class="active">Lesson</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Tracking Lesson {{ courseData.name }}</h2>
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
                  <template v-slot:[`item.tutor`]="{ item }">
                    <div>{{ item.tutor.map((t) => t.name).join(", ") }}</div>
                  </template>
                  <template
                    v-slot:[`item.tracking.average_percentage_completed`]="{
                      item,
                    }"
                  >
                    <div>
                      {{
                        parseFloat(
                          item.tracking.average_percentage_completed
                        ).toFixed(2)
                      }}
                      %
                    </div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="#0056d2"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="viewEnroll(item)"
                        >
                          <v-icon color="white">
                            mdi-eye-arrow-left-outline
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Peserta</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
        <!-- Modal Block / Unblock item -->
        <v-dialog
          v-model="dialogDelete"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogDelete">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to delete
                {{
                  selectedItem.course_category.name
                    ? '"' + selectedItem.course_category.name + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitDelete()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Delete" }}
                  </button>
                  <a
                    @click="dialogDelete.value = false"
                    class="btn btn-text btn-sm tf"
                    >Cancel</a
                  >
                </div>
              </v-card-text>
            </v-card>
          </template>
        </v-dialog>
      </div>
    </div>
  </div>
</template>

<script>
import Header from "@/components/HeaderCMS";
import Sidebar from "@/components/SidebarCMS";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import moment from "moment";
export default {
  name: "LessonPage",
  metaInfo() {
    return {
      title: "Lesson - AcmeLMS",
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
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      isLoading: false,
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      dialogUnblock: false,
      id: "",
      courseCategoryId: "",
      courseData: [],
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

      headers: [
        {
          text: "Name",
          value: "name",
          align: "start",
          width: "30%",
        },
        {
          text: "Jumlah Peserta Selesai",
          value: "tracking.number_user_completed",
          width: "20%",
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
    viewEnroll(item) {
      this.$router.push(
        `/admin/tracking/course-category/${this.courseCategoryId}/course/${this.courseId}/lesson/${item.id}/enroll`
      );
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
          url: `/v1/admin/lesson?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&courses_id=${this.courseId}`,
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
    date(val) {
      return moment(new Date(val)).format("DD MMMM YYYY");
    },

    getCategoryData() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course/read/${this.courseId}`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.courseData = res.data.data.Course;

            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.course_category_id = "";
      } else {
        this.id = item.id;
        this.course_category_id = item.course_category_id;
      }
      this.dialog = true;
      this.getCategoryData();
    },

    alert(item, group) {
      if (group == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = item;
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
  mounted() {
    this.courseCategoryId = this.$route.params.course_category_id;
    this.courseId = this.$route.params.course_id;
    this.getMainData();
    this.getCategoryData();
    this.$root.cms_tab = "tracking/course-category";
  },
};
</script>
