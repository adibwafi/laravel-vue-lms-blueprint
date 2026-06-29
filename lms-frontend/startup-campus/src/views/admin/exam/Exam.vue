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
                <router-link to="/admin/course">{{
                  dataParent.name
                }}</router-link>
                <div class="active">Exam</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Exam</h2>
                <div v-if="lenghtData">
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
                  <template v-slot:[`item.use_question_bank`]="{ item }">
                    <div v-if="item.use_question_bank">
                      <v-chip color="success"> Ya </v-chip>
                    </div>
                    <div v-else>
                      <v-chip color="pink" dark> Tidak </v-chip>
                    </div>
                  </template>
                  <template v-slot:[`item.max_attempt`]="{ item }">
                    <div v-if="item.max_attempt">
                      {{ item.max_attempt }}
                    </div>
                    <div v-else>-</div>
                  </template>
                  <template v-slot:[`item.kkm`]="{ item }">
                    <div v-if="item.kkm">
                      {{ item.kkm }}
                    </div>
                    <div v-else>-</div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom v-if="item.use_question_bank">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="seeQuestionBank(item)"
                        >
                          <v-icon dark> mdi-bank </v-icon>
                        </v-btn>
                      </template>
                      <span>See Question Bank Page</span>
                    </v-tooltip>

                    <v-tooltip bottom v-if="!item.use_question_bank">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="seeExamPage(item)"
                        >
                          <v-icon dark>
                            mdi-format-list-bulleted-square
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>See All Exam Page</span>
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
        <!-- Modal Add / Edit item -->
        <v-dialog
          v-model="dialog"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark>
              {{ id ? "Edit Item" : "Add New" }}</v-toolbar
            >
            <v-card-text>
              <v-form class="mt-24" ref="form">
                <v-text-field
                  v-model="name"
                  label="Name Exam"
                  placeholder="Enter name Exam"
                  required
                ></v-text-field>
                <div>
                  <p class="f-label">Deskripsi</p>
                  <p class="f-helper">
                    Opsional, akan ditampilkan setiap user akan memulai test.
                    Bisa dituliskan tentang informasi test, tata cara
                    pengerjaan, dan lain sebagainya.
                  </p>
                </div>
                <div class="ck">
                  <ckeditor
                    v-model="description"
                    :config="editorConfig"
                    :editor="editor"
                  ></ckeditor>
                  <div class="ck-count">{{ description.length }}</div>
                </div>
                <v-checkbox
                  label="Acak Soal"
                  v-model="randomize"
                  color="primary"
                >
                </v-checkbox>
                <v-checkbox
                  label="Menggunakan KKM"
                  v-model="have_kkm"
                  color="primary"
                >
                </v-checkbox>
                <v-text-field
                  v-if="have_kkm"
                  v-model="kkm"
                  label="KKM"
                  type="number"
                  placeholder="Enter name Exam"
                  hint="Nilai KKM yang harus dicapai user untuk lulus exam"
                  :rules="kkmRules"
                ></v-text-field>
                <v-checkbox
                  v-model="have_max_attempt"
                  color="primary"
                  label="Batasi attempt user"
                >
                </v-checkbox>
                <v-text-field
                  v-if="have_max_attempt"
                  v-model="max_attempt"
                  label="Maksimum Percobaan User"
                  hint="Maksimum percobaan yang bisa dilakukan user dalam mengerjakan exam"
                  type="number"
                  placeholder="Enter name Exam"
                  :rules="max_attemptRules"
                ></v-text-field>
                <v-checkbox
                  v-model="use_question_bank"
                  color="primary"
                  label="Menggunakan Bank Soal"
                >
                  <template v-slot:label>
                    <div>
                      Menggunakan Bank Soal
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            color="primary"
                            fab
                            dark
                            x-small
                            v-on="on"
                            style="margin-left: 8px"
                          >
                            <v-icon> mdi mdi-help </v-icon>
                          </v-btn>
                        </template>
                        Jika menggunakan bank soal, maka setiap peserta akan
                        mendapatkan bank soal secara acak, soal yang akan
                        ditampilkan akan diambil dari bank soal tersebut.
                        Seperti sistem paket soal pada ujian nasional.
                      </v-tooltip>
                    </div>
                  </template>
                </v-checkbox>
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

import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
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
      lenghtData: true,
      course_id: "",
      lesson_id: "",
      id: "",
      name: "",
      have_kkm: false,
      description: "",
      kkm: null,
      have_max_attempt: false,
      use_question_bank: false,
      max_attempt: null,
      randomize: false,
      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,
      nameRules: [(v) => !!v || "Name is required"],
      kkmRules: [
        (v) => (v >= 0 && v <= 100) || "KKM must be between 0 and 100",
      ],
      max_attemptRules: [(v) => v > 0 || "Max Attempt must be greater than 0"],

      editor: ClassicEditor,
      editorConfig: {
        toolbar: [
          "Bold",
          "Italic",
          "|",
          "NumberedList",
          "BulletedList",
          "|",
          "Link",
          "|",
          "Blockquote",
          "|",
          "Undo",
          "Redo",
          "|",
        ],
      },

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
          width: "35%",
        },
        {
          text: "KKM",
          value: "kkm",
          align: "start",
          width: "10%",
        },
        {
          text: "Max Attempt",
          value: "max_attempt",
          align: "start",
          width: "10%",
        },
        {
          text: "Menggunakan Bank Soal",
          value: "use_question_bank",
          align: "start",
          width: "10%",
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
          url: `/v1/admin/course/read/${id}`,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.dataParent = res.data.data.Course;
            return;
          } else {
            this.$router.push({
              path: `/admin/course`,
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
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0]
        ? this.options.sortDesc[0]
        : "ASC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&course_id=${this.$route.params.course_id}&lesson_id=${this.$route.params.lesson_id}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.exam.rows;
            // console.log(
            //   "🚀 ~ file: Exam.vue:406 ~ .then ~ datarow:",
            //   this.datarow
            // );
            this.totalData = res.data.data.exam.count;
            if (this.datarow.length == 0) {
              this.lenghtData = true;
            } else {
              this.lenghtData = false;
            }
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

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.name = "";
        this.have_kkm = false;
        this.description = "";
        this.kkm = null;
        this.have_max_attempt = false;
        this.use_question_bank = false;
        this.max_attempt = null;
        this.randomize = false;
      } else {
        this.id = item.id;
        this.name = item.name;
        this.description = item.description ?? "";
        this.have_kkm = item.kkm ? true : false;
        this.kkm = item.kkm;
        this.have_max_attempt = item.max_attempt ? true : false;
        this.use_question_bank = item.use_question_bank ? true : false;
        this.max_attempt = item.max_attempt;
        this.randomize = !!item.randomize;
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
    },

    submit() {
      const formData = {
        name: this.name,
        course_id: this.course_id,
        lesson_id: this.lesson_id,
        description: this.description,
        max_attempt: this.have_max_attempt ? this.max_attempt : null,
        use_question_bank: this.use_question_bank ? 1 : 0,
        kkm: this.have_kkm ? this.kkm : null,
        randomize: this.randomize ? 1 : 0,
      };
      // console.log(formData);
      this.$root
        .upload("post", "/v1/admin/exam/create", formData)
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
        course_id: this.course_id,
        lesson_id: this.lesson_id,
        max_attempt: this.have_max_attempt ? this.max_attempt : null,
        description: this.description,
        kkm: this.have_kkm ? this.kkm : null,
        use_question_bank: this.use_question_bank ? 1 : 0,
        randomize: this.randomize ? 1 : 0,
      };
      this.$root
        .upload("post", `/v1/admin/exam/update/${this.id}`, formData)
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

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/exam/delete/${this.id}`,
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
    seeQuestionBank(item) {
      this.$router.push({
        path: `/admin/course/${this.course_id}/exam/${item.id}/question-bank`,
      });
    },
    seeExamPage(item) {
      this.$router.push({
        path: `/admin/course/${this.course_id}/exam/${item.id}/page`,
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

    getTime(time) {
      var minutes = Math.floor(time / 60);
      var seconds = time - minutes * 60;

      return `${minutes} min ${seconds} sec`;
    },
  },
  mounted() {
    this.getMainData();
    const course_id = this.$route.params.course_id;
    const lesson_id = this.$route.params.lesson_id;
    if (course_id) {
      this.course_id = course_id;
      this.getParentData(course_id);
    }
    if (lesson_id) {
      this.lesson_id = lesson_id;
    }
    this.$root.cms_tab = "course";
  },
};
</script>
<style lang="scss" scoped>
.f-label {
  font-weight: 500;
}

.f-helper {
  font-weight: 400;
  font-size: 0.8rem;
  margin-top: -0.8rem;
}
</style>
