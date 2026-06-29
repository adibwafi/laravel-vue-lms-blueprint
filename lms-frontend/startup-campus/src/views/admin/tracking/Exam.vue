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
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Tracking Exam</h2>
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
                  <template v-slot:[`item.type`]="{ item }">
                    <v-chip
                      :color="item.is_quiz ? 'primary' : 'secondary'"
                      rounded
                      retain-focus-on-click
                      >{{ item.is_quiz ? "Kuis" : "Ujian" }}</v-chip
                    >
                  </template>
                  <template v-slot:[`item.courseCategory`]="{ item }">
                    {{ extractCourseCategoryName(item) }}
                  </template>
                  <template v-slot:[`item.course`]="{ item }">
                    {{ extractCourseName(item) }}
                  </template>
                  <template v-slot:[`item.lesson`]="{ item }">
                    {{ extractLessonName(item) }}
                  </template>
                  <template v-slot:[`item.tracking.average_score`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <p v-bind="attrs" v-on="on">
                          {{ item.tracking.average_score.toFixed(2) }}
                        </p>
                      </template>
                      <span
                        >Rata-rata nilai dari keseluruhan peserta (Semua attempt
                        dihitung)</span
                      >
                    </v-tooltip>
                  </template>
                  <template
                    v-slot:[`item.tracking.average_highest_score`]="{ item }"
                  >
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <p v-bind="attrs" v-on="on">
                          {{ item.tracking.average_highest_score.toFixed(2) }}
                        </p>
                      </template>
                      <span
                        >Rata-rata nilai tertinggi dari semua attempt
                        masing-masing peserta (Misalnya peserta attempt 3x, maka
                        dihitung nilai yang paling tinggi saja)</span
                      >
                    </v-tooltip>
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
                          @click="viewScoreDetail(item)"
                        >
                          <v-icon color="white"> mdi-star </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Detail Nilai</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="green"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="viewAnswerDetail(item)"
                        >
                          <v-icon color="white"> mdi-chart-bar </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Summary Jawaban</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="teal"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="viewAllAnswerDetail(item)"
                        >
                          <v-icon color="white"> mdi-eye </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Semua Jawaban</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
        <!-- Modal Add / Edit item -->
        <v-dialog
          v-model="dialog"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark>
              Edit Status {{ selectedItem.name }}</v-toolbar
            >
            <v-card-text>
              <v-form class="mt-24">
                <v-select
                  v-model="status"
                  :items="statusItems"
                  label="Select Status"
                ></v-select>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="submitStatus()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Update" }}
                </button>
                <a @click="dialog = false" class="btn btn-text btn-sm tf"
                  >Cancel</a
                >
              </div>
            </v-card-text>
          </v-card>
        </v-dialog>
        <!-- Modal Delete item -->
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
                  selectedItem.item
                    ? '"' + selectedItem.item + '"'
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
export default {
  name: "UserExamPage",
  metaInfo() {
    return {
      title: "User Exam - AcmeLMS",
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
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      id: "",
      status: "",
      statusItems: ["draft", "released", "disabled"],

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,

      // table
      search: "",
      datarow: [],
      totalData: 0,
      options: {},
      typingTimer: 0,
      selectedItem: {},

      headers: [
        {
          text: "Course Category",
          value: "courseCategory",
          align: "start",
          width: "10%",
          sortable: false,
        },
        {
          text: "Course",
          value: "course",
          align: "start",
          width: "10%",
          sortable: false,
        },
        {
          text: "Lesson",
          value: "lesson",
          align: "start",
          width: "10%",
          sortable: false,
        },
        {
          text: "Tipe",
          value: "type",
          width: "10%",
        },
        {
          text: "Name",
          value: "name",
          width: "10%",
        },
        {
          text: "KKM",
          value: "kkm",
          width: "5%",
        },
        {
          text: "Jumlah Peserta Selesai",
          value: "tracking.number_user_completed",
          width: "8%",
        },
        {
          text: "Rata-Rata Nilai Keseluruhan",
          value: "tracking.average_score",
          width: "8%",
        },
        {
          text: "Rata-Rata Nilai Tertinggi",
          value: "tracking.average_highest_score",
          width: "7%",
        },
        {
          text: "Action",
          value: "action",
          width: "20%",
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
    extractCourseCategoryName(item) {
      if (item.is_quiz) {
        return item.lesson?.course?.course_category?.name;
      } else {
        return item.course?.course_category?.name;
      }
    },
    extractCourseName(item) {
      // console.log("🚀 ~ file: Exam.vue:265 ~ extractCourseName ~ item", item);
      if (item.is_quiz) {
        return item.lesson?.course?.name;
      } else {
        return item.course?.name;
      }
    },
    extractLessonName(item) {
      if (item.is_quiz) {
        return item.lesson?.name;
      } else {
        return "-";
      }
    },
    viewScoreDetail(item) {
      this.$router.push(`/admin/tracking/exam/${item.id}/score`);
    },
    viewAnswerDetail(item) {
      this.$router.push(`/admin/tracking/exam/${item.id}/answer`);
    },
    viewAllAnswerDetail(item) {
      this.$router.push(`/admin/tracking/exam/${item.id}/answer-all`);
    },
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
          url: `/v1/admin/exam?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&with_course=true&with_course_category=true&with_lesson=true`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.exam.rows;
            this.totalData = res.data.data.exam.count;
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
      this.textNotif = item;
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
  mounted() {
    this.getMainData();
    this.$root.cms_tab = "tracking/exam";
  },
};
</script>
