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
                <div class="active">Peserta</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Peserta Course {{ courseData.name }}</h2>
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
                  <template v-slot:[`item.user.image`]="{ item }">
                    <div class="dataimg">
                      <img
                        v-if="item.user.image"
                        :src="`${storageBaseUrl}` + item.user.image"
                        alt=""
                        width="200"
                        height="200"
                        class="imgs"
                      />
                      <img
                        src="@/assets/img/img-placeholder.jpeg"
                        alt=""
                        v-else
                      />
                    </div>
                  </template>
                  <template v-slot:[`item.user_complete`]="{ item }">
                    <div>
                      {{ item.user_complete ? "✅" : "❌" }}
                    </div>
                  </template>
                  <template v-slot:[`item.user_progress_percentage`]="{ item }">
                    <div>{{ item.user_progress_percentage }} %</div>
                  </template>
                  <template v-slot:[`item.created_at`]="{ item }">
                    <div>
                      {{ date(item.created_at) }}
                    </div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="teal"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="openDialog(item)"
                        >
                          <v-icon dark> mdi-pencil </v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="red"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="deleteItem(item)"
                        >
                          <v-icon dark> mdi-delete </v-icon>
                        </v-btn>
                      </template>
                      <span>Delete</span>
                    </v-tooltip>
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

<script>
import Header from "@/components/HeaderCMS";
import Sidebar from "@/components/SidebarCMS";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import moment from "moment";
export default {
  name: "CourseEnrollPage",
  metaInfo() {
    return {
      title: "Course Enroll - AcmeLMS",
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
      course_category_id: "",
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
          text: "Image",
          value: "user.image",
          width: "15%",
        },
        {
          text: "Name",
          value: "user.fullname",
          width: "15%",
        },
        {
          text: "Email",
          value: "user.email",
          width: "15%",
        },
        {
          text: "Telefon",
          value: "user.phone",
          width: "15%",
        },
        {
          text: "Selesai",
          value: "user_complete",
          width: "10%",
        },
        {
          text: "Progress",
          value: "user_progress_percentage",
          width: "10%",
        },
        {
          text: "Tanggal Enroll",
          value: "created_at",
          width: "15%",
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
    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0] ? "ASC" : "DESC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/user-course?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&course_id=${this.courseId}&with_user=true`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.UserCourse.rows;
            this.totalData = res.data.data.UserCourse.count;
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

    getCourseData() {
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
    this.getCourseData();
    this.$root.cms_tab = "tracking/course-category";
  },
};
</script>
