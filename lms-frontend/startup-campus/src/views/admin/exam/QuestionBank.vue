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
                <router-link to="/admin/course">Course</router-link>
                <router-link :to="`/admin/course/${this.course_id}/lesson`"
                  >Lesson</router-link
                >
                <div class="active">Question Bank</div>
              </div>
              <div class="breadcrumbs" v-else>
                <router-link to="/admin/course">
                  {{ dataParent?.course?.name }}
                </router-link>
                <router-link :to="`/admin/course/${this.course_id}/exam`">
                  {{ dataParent?.name }}
                </router-link>
                <div class="active">Question Bank</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Question Bank</h2>
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
                    Import Question Bank
                  </button>
                  <button class="btn btn-blue btn-sm" @click="openDialog()">
                    Add New
                  </button>
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
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <router-link
                          :to="`/admin/course/${course_id}/exam/${exam_id}/question-bank/${item.id}/page`"
                        >
                          <v-btn
                            class="mrb-8"
                            fab
                            x-small
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-format-list-bulleted-square
                            </v-icon>
                          </v-btn>
                        </router-link>
                      </template>
                      <span>Tambahkan Soal</span>
                    </v-tooltip>
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
            <v-toolbar color="#09408e" dark> Import Bank Soal</v-toolbar>
            <v-card-text>
              <v-form
                class="mt-24"
                :style="{
                  marginTop: '24px',
                }"
                ref="form"
              >
                <v-autocomplete
                  v-model="question_bank_id"
                  :items="question_banks"
                  :item-text="getQuestionBankSelectLabel"
                  item-value="id"
                  placeholder="Bank Soal untuk diimport"
                  label="Bank Soal untuk diimport"
                  required
                  outlined
                  dense
                  clearable
                ></v-autocomplete>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="importQuestionBank()"
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
        <!--  Dialog add -->
        <v-dialog
          v-model="dialog"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark>
              {{ id ? "Edit Bank Soal" : "Tambah Bank Soal" }}</v-toolbar
            >
            <v-card-text>
              <v-form class="mt-24" ref="form">
                <v-text-field
                  v-model="name"
                  label="Nama Bank Soal"
                  placeholder="Enter name Exam"
                  required
                ></v-text-field>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  v-if="id == ''"
                  @click="submit()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Save" }}
                </button>
                <button
                  v-if="id"
                  @click="update()"
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
  name: "QuestionBankPage",
  metaInfo() {
    return {
      title: "Question Bank - AcmeLMS",
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
      question_banks: [],
      question_bank_id: "",
      course_id: "",
      lesson_id: "",
      exam_id: "",
      id: "",
      name: "",

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
    getParentData(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.dataParent = res.data.data.exam;
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

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/question-bank?search=${search}&page=${page}&limit=${itemsPerPage}&exam_id=${this.$route.params.id}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.QuestionBanks.rows;
            // console.log(
            //   "🚀 ~ file: QuestionBank.vue:299 ~ .then ~ datarow:",
            //   this.datarow
            // );
            this.totalData = res.data.data.QuestionBanks.count;
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

    getAllQuestionBankData() {
      return this.$root
        .axios({
          method: "get",
          url: `/v1/admin/question-bank?limit=99999999&withCourseCategory=true&withCourse=true&withExam=true`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.question_banks = res.data.data.QuestionBanks.rows;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },

    importQuestionBank() {
      const payload = {
        question_bank_id: this.question_bank_id,
        exam_id: this.exam_id,
      };

      return this.$root
        .axios({
          method: "post",
          url: `/v1/admin/question-bank/import`,
          data: payload,
        })
        .then((res) => {
          if (!res.data.error) {
            this.dialogImport = false;
            this.getMainData();
            return;
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

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.name = "";
      } else {
        this.id = item.id;
        this.name = item.name;
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    submit() {
      const formData = {
        name: this.name,
        exam_id: this.exam_id,
      };
      this.$root
        .upload("post", "/v1/admin/question-bank/create", formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            setTimeout(() => {
              this.dialog = false;
              this.getMainData();
              this.addLoading = false;

              this.alert(res.data.message, "success");
            }, 1000);
            return;
          } else {
            if (res.data.message == "Error validation") {
              this.alert("Semua field wajib diisi", "danger");
            } else {
              this.alert(res.data.message, "danger");
            }
            this.addLoading = false;
          }
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },

    update() {
      this.addLoading = true;
      const formData = {
        name: this.name,
        exam_id: this.exam_id,
      };
      this.$root
        .upload("post", `/v1/admin/question-bank/update/${this.id}`, formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            setTimeout(() => {
              this.dialog = false;
              this.getMainData();
              this.addLoading = false;

              this.alert(res.data.message, "success");
            }, 1000);

            return;
          } else {
            this.alert(res.data.message, "danger");
            this.addLoading = false;
          }
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },

    getQuestionBankSelectLabel(item) {
      const name = item.name;
      const exam = item.exam;
      const course = exam.course;
      const course_category = course.course_category;

      return `${name} - ${exam.name} - ${course_category.name}`;
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/question-bank/delete/${this.id}`,
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
    this.getAllQuestionBankData();
    const course_id = this.$route.params.course_id;
    const lesson_id = this.$route.params.lesson_id;
    const exam_id = this.$route.params.id;
    if (course_id && exam_id) {
      this.course_id = course_id;
      this.exam_id = exam_id;
      this.lesson_id = lesson_id;
      this.getParentData(exam_id);
    }
    this.$root.cms_tab = "course";
  },
};
</script>
