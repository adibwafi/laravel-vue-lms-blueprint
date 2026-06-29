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
              <div class="breadcrumbs" v-if="lesson_id">
                <router-link to="/admin/course"> Course</router-link>
                <router-link :to="`/admin/course/${this.course_id}/lesson`">
                  {{ dataParent?.name }}
                </router-link>
                <div class="active">Exam Page</div>
              </div>
              <div class="breadcrumbs" v-else-if="question_bank_id">
                <router-link to="/admin/course">
                  {{ dataParent?.course?.name }}
                </router-link>
                <router-link :to="`/admin/course/${this.course_id}/exam`">
                  {{ dataParent?.name }}
                </router-link>
                <router-link
                  :to="`/admin/course/${this.course_id}/exam/${this.exam_id}/question-bank`"
                  >Question Bank</router-link
                >
                <div class="active">Exam Page</div>
              </div>
              <div class="breadcrumbs" v-else>
                <router-link to="/admin/course">
                  {{ dataParent?.course?.name }}
                </router-link>
                <router-link :to="`/admin/course/${this.course_id}/exam`">
                  {{ dataParent?.name }}
                </router-link>
                <div class="active">Exam Page</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2 v-if="question_bank_id">
                  {{ dataQuestionBank.name }} - Exam Page
                </h2>
                <h2 v-else>{{ dataParent.name }} - Exam Page</h2>

                <div
                  :style="{
                    display: 'flex',
                    justifyContent: 'flex-end',
                    gap: '24px',
                  }"
                >
                  <button
                    class="btn btn-sm"
                    :style="{
                      background: '#09408e',
                      color: '#fff',
                    }"
                    @click="openImportDialog()"
                  >
                    Import Exam Page
                  </button>
                  <router-link
                    :to="`/admin/course/${course_id}${
                      lesson_id ? '/lesson/' + lesson_id : ''
                    }/exam/${exam_id}${
                      question_bank_id
                        ? '/question-bank/' + question_bank_id
                        : ''
                    }/page/add`"
                    class="btn btn-blue btn-sm"
                    >Add New</router-link
                  >
                </div>
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
                  <template v-slot:[`item.action`]="{ item }">
                    <v-btn
                      class="mrb-8"
                      fab
                      x-small
                      v-if="item.sorter < datarow.length"
                      @click="moveDown(item)"
                    >
                      <v-icon dark> mdi-chevron-down </v-icon>
                    </v-btn>
                    <v-btn
                      class="mrb-8"
                      fab
                      x-small
                      v-if="item.sorter > 1"
                      @click="moveUp(item)"
                    >
                      <v-icon dark> mdi-chevron-up </v-icon>
                    </v-btn>
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
                          @click="goEdit(item.id)"
                        >
                          <v-icon dark> mdi-pencil </v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="pink"
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
        <!--  Dialog Import -->
        <v-dialog
          v-model="dialogImport"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark> Import Exam Page</v-toolbar>
            <v-card-text
              v-if="isLoadingGetAllExam"
              :style="{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                height: '200px',
                padding: '16px',
                gap: '8px',
              }"
            >
              <v-progress-circular
                indeterminate
                color="primary"
              ></v-progress-circular>
              <div>Loading...</div>
            </v-card-text>
            <v-card-text v-else>
              <v-form
                class="mt-24"
                :style="{
                  marginTop: '24px',
                }"
                ref="form"
              >
                <v-autocomplete
                  v-model="import_exam_id"
                  :items="exams"
                  :item-text="getExamSelectLabel"
                  item-value="id"
                  placeholder="Exam Page untuk diimport"
                  label="Exam Page untuk diimport"
                  required
                  outlined
                  dense
                  clearable
                  append-outer-icon="mdi-restart"
                  @click:append-outer="resetExamCache"
                ></v-autocomplete>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="importExam()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  Import
                </button>
                <a @click="dialogImport = false" class="btn btn-text btn-sm tf"
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
  name: "ExamPage",
  metaInfo() {
    return {
      title: "Exam - AcmeLMS",
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
      dialogImport: false,
      course_id: "",
      lesson_id: "",
      exam_id: "",
      question_bank_id: "",
      id: "",

      import_exam_id: "",
      exams: [],
      isLoadingGetAllExam: false,

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
      dataQuestionBank: "",

      headers: [
        {
          text: "Name",
          value: "name",
          align: "start",
          width: "50%",
        },
        {
          text: "Content Type",
          value: "content_type",
          sortable: false,
          width: "25%",
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
    getExamSelectLabel(item) {
      const name = item.name;
      const lesson = item?.lesson;
      if (lesson) {
        return `${lesson?.course?.name} - ${lesson?.name} - ${name} (Quiz)`;
      }

      const course = item?.course;
      if (course) {
        return `${course?.course_category?.name} - ${course?.name} - ${name} (Exam)`;
      }

      return `${name}`;
    },
    resetExamCache() {
      // console.log("resetExamCache");
      window.localStorage.removeItem("allExams");
      this.getAllExamData();
    },
    getAllExamData() {
      this.isLoadingGetAllExam = true;
      const allExams = window.localStorage.getItem("allExams");
      if (allExams) {
        this.isLoadingGetAllExam = false;
        this.exams = JSON.parse(allExams);
        // console.log(
        //   "🚀 ~ file: ExamPage.vue:374 ~ getAllExamData ~ exams:",
        //   this.exams
        // );
      } else {
        this.$root
          .axios({
            method: "get",
            url: `/v1/admin/exam?limit=99999999&with_course=true&with_lesson=true&with_course_category=true`,
          })
          .then((res) => {
            if (!res.data.error) {
              this.exams = res.data.data.exam.rows;
              // console.log(
              //   "🚀 ~ file: ExamPage.vue:343 ~ .then ~ exams:",
              //   this.exams
              // );
              window.localStorage.setItem(
                "allExams",
                JSON.stringify(this.exams)
              );
              return;
            }
          })
          .catch((e) => {
            console.log(e);
          })
          .finally(() => {
            this.isLoadingGetAllExam = false;
          });
      }

      this.exams = this.exams.filter((val) => val.lesson || val.course);
    },

    importExam() {
      const payload = {
        current_exam_id: this.exam_id,
        import_exam_id: this.import_exam_id,
      };
      // console.log(
      //   "🚀 ~ file: ExamPage.vue:414 ~ importExam ~ payload:",
      //   payload
      // );

      return this.$root
        .axios({
          method: "post",
          url: `/v1/admin/exam/import`,
          data: payload,
        })
        .then((res) => {
          // console.log("🚀 ~ file: ExamPage.vue:423 ~ .then ~ res:", res);
          if (res.data.success) {
            this.dialogImport = false;
            this.getMainData();
            this.alert("Sukses melakukan import exam page", "success");
          } else {
            const message = res.data.message;
            if (message) {
              this.alert(message, "danger");
            } else {
              this.alert("Something went wrong", "danger");
            }
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },

    openImportDialog() {
      this.dialogImport = true;
    },
    getQuestionBankData(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/question-bank/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.dataQuestionBank = res.data.data.QuestionBank;
            return;
          } else {
            this.$router.push({
              path: `/admin/course/${this.course_id}/exam/${this.exam_id}/question-bank`,
            });
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getParentData(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam/read/${id}`,
        })
        .then((res) => {
          // console.log("parent", res);
          if (res.data.success) {
            this.dataParent = res.data.data.exam;
            if (this.dataParent.use_question_bank && !this.question_bank_id) {
              this.$router.push({
                path: `/admin/course/${this.course_id}/exam/${this.exam_id}/question-bank`,
              });
            }
            return;
          } else {
            this.$router.push({
              path: `/admin/course/${this.course_id}/exam`,
            });
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
      var questionBankId = this.question_bank_id ? this.question_bank_id : "";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam-page?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&exam_id=${this.$route.params.id}&question_bank_id=${questionBankId}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.exam.rows;
            // console.log(this.datarow.map((item) => item.sorter));
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

    moveUp(item) {
      this.isLoading = true;
      var data = {
        id: item.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/exam-page/moveup`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.getMainData();
        });
    },

    moveDown(item) {
      this.isLoading = true;
      var data = {
        id: item.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/exam-page/movedown`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.getMainData();
        });
    },

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.name = "";
        this.color = "";
      } else {
        this.id = item.id;
        this.name = item.name;
        this.color = item.color;
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/exam-page/delete/${this.id}`,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          } else {
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialogDelete = false;
          this.getMainData();
        });
    },

    goEdit(id) {
      this.$router.push({
        path: `/admin/course/${this.course_id}${
          this.lesson_id ? "/lesson/" + this.lesson_id : ""
        }/exam/${this.exam_id}${
          this.question_bank_id ? "/question-bank/" + this.question_bank_id : ""
        }/page/${id}/edit`,
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
    const course_id = this.$route.params.course_id;
    const lesson_id = this.$route.params.lesson_id;
    const question_bank_id = this.$route.params.question_bank_id;
    const exam_id = this.$route.params.id;
    if (course_id && exam_id) {
      this.course_id = course_id;
      this.exam_id = exam_id;
      this.lesson_id = lesson_id;
      this.question_bank_id = question_bank_id;
      if (this.question_bank_id) {
        this.getQuestionBankData(this.question_bank_id);
      }
      this.getParentData(exam_id);
    }
    this.getMainData();
    this.$root.cms_tab = "course";
    this.getAllExamData();
  },
};
</script>
