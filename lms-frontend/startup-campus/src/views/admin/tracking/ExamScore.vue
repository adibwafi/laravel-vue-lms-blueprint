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
                <router-link to="/admin/tracking/exam">Exam</router-link>
                <div class="active">Nilai</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Tracking Nilai Exam</h2>
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
                  :expanded.sync="expanded"
                  item-key="user_id"
                  show-expand
                >
                  <template v-slot:expanded-item="{ headers, item }">
                    <td :colspan="headers.length">
                      <h4 class="mt-4 mb-5">
                        Semua Attempt Ujian Ini dari {{ item.user.fullname }}
                      </h4>
                      <v-simple-table>
                        <template v-slot:default>
                          <thead>
                            <tr>
                              <th class="text-left">Nomor</th>
                              <th class="text-left">Jawaban Benar</th>
                              <th class="text-left">Score</th>
                              <th class="text-left">Waktu Mulai</th>
                              <th class="text-left">Waktu Selesai</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(up, index) in item.user_poin"
                              :key="up.id"
                            >
                              <td>{{ index + 1 }}</td>
                              <td>{{ up.poin }}</td>
                              <td>{{ up.score }}</td>
                              <td>{{ getDate(up.created_at) }}</td>
                              <td>{{ getDate(up.finished_at) }}</td>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </td>
                  </template>
                  <template v-slot:[`item.user.image`]="{ item }">
                    <div class="dataimg">
                      <img
                        v-if="item.user.image"
                        :src="`${storageBaseUrl}` + item.user.image"
                        alt=""
                        width="50"
                        height="50"
                        class="imgs"
                      />
                      <img
                        src="@/assets/img/img-placeholder.jpeg"
                        alt=""
                        v-else
                      />
                    </div>
                  </template>
                  <template v-slot:[`item.type`]="{ item }">
                    <v-chip
                      :color="item.is_quiz ? 'primary' : 'secondary'"
                      rounded
                      retain-focus-on-click
                      >{{ item.is_quiz ? "Kuis" : "Ujian" }}</v-chip
                    >
                  </template>
                  <template v-slot:[`item.average_poin`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <p v-bind="attrs" v-on="on">
                          {{ parseFloat(item.average_poin).toFixed(1) }}
                        </p>
                      </template>
                      <span>Rata-rata jawaban benar dari exam ini</span>
                    </v-tooltip>
                  </template>
                  <template v-slot:[`item.highest_score`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <p v-bind="attrs" v-on="on">
                          {{ item.highest_score.toFixed(1) }}
                        </p>
                      </template>
                      <span>Nilai tertinggi </span>
                    </v-tooltip>
                  </template>
                  <template v-slot:[`item.score`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <p v-bind="attrs" v-on="on">
                          {{ item.score.toFixed(1) }}
                        </p>
                      </template>
                      <span>Nilai tertinggi </span>
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
                          <v-icon color="white">
                            mdi-eye-arrow-left-outline
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Detail Nilai</span>
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
  name: "UserExamScorePage",
  metaInfo() {
    return {
      title: "Exam Score - AcmeLMS",
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
      examId: "",

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
      expanded: [],

      headers: [
        {
          text: "Image",
          value: "user.image",
          width: "10%",
        },
        {
          text: "Name",
          value: "user.fullname",
          width: "10%",
        },
        {
          text: "Email",
          value: "user.email",
          width: "10%",
        },
        {
          text: "Telefon",
          value: "user.phone",
          width: "10%",
        },
        {
          text: "Rata-rata jawaban benar",
          value: "average_poin",
          width: "5%",
        },
        {
          text: "Rata-rata skor",
          value: "score",
          width: "5%",
        },
        {
          text: "Skor Tertinggi",
          value: "highest_score",
          width: "5%",
        },
        {
          text: "Jumlah Attempt",
          value: "number_of_attempt",
          width: "5%",
        },
        { text: "Detail", value: "data-table-expand", width: "5%" },
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
    getDate(dateStr) {
      try {
        if (!dateStr) {
          return "-";
        }

        return new Date(dateStr).toLocaleString("id-ID") + " WIB";
      } catch (error) {
        return "-";
      }
    },
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
        return item.lesson.name;
      } else {
        return "-";
      }
    },
    viewScoreDetail(item) {
      this.$router.push(`/admin/tracking/exam/${item.id}/score`);
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
          url: `/v1/admin/exam-poin?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&with_user=true&groupBy=user_id&exam_id=${this.examId}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.ExamPoin.rows;
            this.totalData = res.data.data.ExamPoin.count;
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
    this.examId = this.$route.params.exam_id;
    this.getMainData();
    this.$root.cms_tab = "tracking/exam";
  },
};
</script>
