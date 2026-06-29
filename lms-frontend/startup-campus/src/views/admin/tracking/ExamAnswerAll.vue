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
                <div class="active">Semua Jawaban</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>All Exam Answer</h2>
                <button class="btn btn-blue btn-sm" @click="exportCSV">
                  Export to CSV
                </button>
              </div>
              <v-card>
                <v-card-title>
                  <v-select
                    clearable
                    v-model="examPageId"
                    :items="examPages"
                    :item-text="getExamPageName"
                    item-value="id"
                    label="Select Exam Page"
                  ></v-select>
                  <v-select
                    clearable
                    v-model="userId"
                    :items="users"
                    :item-text="getUserName"
                    item-value="id"
                    label="Select User"
                  ></v-select>
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
                  item-key="id"
                >
                  <template v-slot:[`item.exam_page.content_type`]="{ item }">
                    <v-chip :color="getColor(item.exam_page)" dark>{{
                      item.exam_page.content_type
                    }}</v-chip>
                  </template>
                  <template v-slot:[`item.correct_answer`]="{ item }">
                    <div v-for="(ans, idx) in item.correct_answer" :key="idx">
                      <p>
                        {{ ans }}
                      </p>
                      <br />
                    </div>
                  </template>
                  <template v-slot:[`item.user_answer`]="{ item }">
                    <div v-for="(ans, idx) in item.user_answer" :key="idx">
                      <p>
                        {{ ans }}
                      </p>
                      <br />
                    </div>
                  </template>
                  <template v-slot:[`item.exam_page.question`]="{ item }">
                    <div
                      class="mt-4 mb-4"
                      v-html="item.exam_page.question"
                    ></div>
                  </template>
                  <template v-slot:[`item.answer`]="{ item }">
                    <v-list dense color="transparent">
                      <v-list-item
                        v-for="(answer, idx) in getAnswer(item)"
                        v-bind:key="idx"
                      >
                        <v-list-item-content>
                          <v-list-item-subtitle>{{
                            answer
                          }}</v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                  </template>
                  <template v-slot:[`item.created_at`]="{ item }">
                    {{ getDate(item.created_at) }}
                  </template>
                  <template v-slot:[`item.is_correct`]="{ item }">
                    <v-chip :color="item.is_correct ? 'green' : 'red'" dark>{{
                      item.is_correct ? "Benar" : "Salah"
                    }}</v-chip>
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
import csvDownload from "json-to-csv-export";
export default {
  name: "AllExamAnswerPage",
  metaInfo() {
    return {
      title: "Exam Answer - AcmeLMS",
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
      examId: "",
      examPageId: "",
      examPages: [],

      userId: "",
      users: [],

      options: {},

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
      typingTimer: 0,
      selectedItem: {},
      expanded: [],

      headers: [
        {
          text: "Nama Exam Page",
          value: "exam_page.name",
          width: "10%",
        },
        {
          text: "Soal",
          value: "exam_page.question",
          width: "20%",
        },
        {
          text: "Content Type",
          value: "exam_page.content_type",
          width: "20%",
        },
        {
          text: "Nama",
          value: "user.fullname",
          width: "10%",
        },
        {
          text: "Telfon",
          value: "user.phone",
          width: "10%",
        },
        {
          text: "Email",
          value: "user.email",
          width: "10%",
        },
        {
          text: "Jawaban User",
          value: "user_answer",
          width: "10%",
        },
        {
          text: "Jawaban Benar",
          value: "correct_answer",
          width: "10%",
        },
        {
          text: "Apakah Benar",
          value: "is_correct",
          width: "10%",
        },
        {
          text: "Waktu Mengerjakan",
          value: "created_at",
          width: "10%",
        },
      ],
    };
  },
  watch: {
    examPageId: {
      handler() {
        this.getMainData();
      },
    },
    userId: {
      handler() {
        this.getMainData();
      },
    },
    options: {
      handler() {
        // console.log("options", this.options);
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
    getColor(item) {
      switch (item.content_type) {
        case "answer":
          return "orange";
        case "fill_blank":
          return "blue";
        case "multiple_choice":
          return "green";
        default:
          return "black";
      }
    },
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
    getAllExamAnswerForThisExam(examPageId = undefined) {
      const queryParam = {
        page: 1,
        limit: 99999,
        exam_id: this.examId,
        exam_page_id: examPageId,
      };
      return this.$root.axios({
        method: "get",
        url: `/v1/admin/exam-answer`,
        params: queryParam,
      });
    },
    async exportCSV() {
      try {
        this.actLoading = true;
        const response = await this.getAllExamAnswerForThisExam();
        const allExamAnswer = response.data.data.exam_answer.rows;

        const exportField = allExamAnswer.map((answer) => {
          return {
            "Nama Exam Page": answer.exam_page.name,
            Soal: answer.exam_page.question,
            "Content Type": answer.exam_page.content_type,
            Nama: answer.user.fullname,
            Telfon: answer.user.phone,
            Email: answer.user.email,
            "Jawaban User": answer.user_answer,
            "Jawaban Benar": answer.correct_answer,
            "Apakah Benar": answer.is_correct ? "Benar" : "Salah",
            "Waktu Mengerjakan": this.getDate(answer.created_at),
          };
        });
        console.log(
          "🚀 ~ file: ExamAnswerAll.vue:215 ~ exportCSV ~ allExamAnswer:",
          exportField
        );
        csvDownload({
          data: exportField,
          filename: "exam-answer.csv",
          delimiter: ",",
          header: [],
        });
      } catch (e) {
        this.alert("error", "Terjadi kesalahan");
        console.log(e);
      } finally {
        this.actLoading = false;
      }
    },
    getUserName(item) {
      return `${item.fullname} - ${item.email}`;
    },
    getExamPageName(item) {
      let question = JSON.parse(item.data).content;
      if (question.length > 50) {
        question = question.substring(0, 50) + "...";
      }

      return `${item.name} - ${question}`;
    },
    getExamPageData() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam-page?page=1&limit=99999&exam_id=${this.examId}`,
        })
        .then((res) => {
          // console.log("🚀 ~ file: ExamAnswerAll.vue:274 ~ .then ~ res:", res);
          if (!res.data.error) {
            this.examPages = res.data.data.exam.rows;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },
    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0] ? "ASC" : "DESC";
      if (this.examPageId == null) {
        this.examPageId = "";
      }

      if (this.userId == null) {
        this.userId = "";
      }

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam-answer?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&exam_id=${this.examId}&exam_page_id=${this.examPageId}&user_id=${this.userId}`,
        })
        .then((res) => {
          // console.log("🚀 ~ file: ExamAnswerAll.vue:282 ~ .then ~ res:", res);
          if (!res.data.error) {
            this.datarow = res.data.data.exam_answer.rows;
            this.totalData = res.data.data.exam_answer.count;
            this.users = res.data.data.user;
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
    this.getExamPageData();
    this.getMainData();
    this.$root.cms_tab = "tracking/exam";
  },
};
</script>
<style lang="scss" scoped>
.v-card__title {
  text-align: center !important;
  font-size: 1rem;
  display: block;
}
</style>
