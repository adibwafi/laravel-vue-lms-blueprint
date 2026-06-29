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
                  :to="`/admin/course?categories_id=${dataParent.categories_id}`"
                  >{{ dataParent.name }}</router-link
                >
                <div class="active">Lesson</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Lesson</h2>
                <button class="btn btn-blue btn-sm" @click="openDialog()">
                  Add New
                </button>
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
                  <template v-slot:[`item.learn_time`]="{ item }">
                    <div>{{ getTime(item.learn_time) }}</div>
                  </template>
                  <template v-slot:[`item.type`]="{ item }">
                    <div>{{ getLessonType(item) }}</div>
                  </template>
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
                        <span v-on="on">
                          <v-btn
                            class="mrb-8"
                            fab
                            x-small
                            v-bind="attrs"
                            v-on="on"
                            :disabled="!!item.using_zoom"
                            @click="
                              item.is_quiz
                                ? seeExamPage(item)
                                : seeLessonPage(item)
                            "
                          >
                            <v-icon dark>
                              {{
                                item.is_quiz
                                  ? "mdi-sticker-text "
                                  : "mdi-format-list-bulleted-square"
                              }}
                            </v-icon>
                          </v-btn>
                        </span>
                      </template>
                      <span>{{
                        item.is_quiz
                          ? "See All Quiz Page"
                          : item.using_zoom
                          ? "Tidak bisa menambah lesson page karena lesson ini menggunakan zoom"
                          : "See All Lesson Page"
                      }}</span>
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
              <v-form class="mt-24">
                <v-text-field
                  v-model="name"
                  label="Name Lesson"
                  placeholder="Enter name lesson"
                  required
                ></v-text-field>
                <v-select
                  label="Pilih Scope"
                  v-model="scope"
                  :items="scopeList"
                  item-text="name"
                  item-value="key"
                  clearable
                ></v-select>
                <v-checkbox
                  color="primary"
                  v-model="is_quiz"
                  :label="`Ini adalah Quiz`"
                  class="check"
                >
                </v-checkbox>
                <div v-if="is_quiz" class="quiz-form">
                  <h4>Opsi Quiz</h4>
                  <div class="mt-5 deskripsi">
                    <p class="v-label">Deskripsi</p>
                    <p class="v-helper">
                      Opsional, akan ditampilkan setiap user akan memulai test.
                      Bisa dituliskan tentang informasi test, tata cara
                      pengerjaan, dan lain sebagainya.
                    </p>
                  </div>
                  <div class="ck">
                    <ckeditor
                      v-model="description"
                      :editor="editor"
                      :config="editorConfig"
                    ></ckeditor>
                    <div class="ck-count">{{ description.length }}</div>
                  </div>
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
                    label="Ini adalah Journal Reflective"
                    v-model="is_journal_reflection"
                    color="primary"
                  >
                  </v-checkbox>
                </div>
                <span v-if="!haveOptionZoom"
                  >Untuk bisa menggunakan zoom pada lesson, course pada lesson
                  ini harus mempunyai link zoom.</span
                >
                <div class="using-zoom-group">
                  <v-checkbox
                    color="primary"
                    v-model="using_zoom"
                    :label="`Menggunakan Zoom`"
                    :disabled="!haveOptionZoom || is_quiz"
                    class="check"
                  >
                  </v-checkbox>
                  <p v-if="is_quiz">
                    Tidak bisa menggunakan zoom jika lesson ini adalah quiz.
                  </p>
                  <p v-if="using_zoom">
                    Pengaturan link zoom dapat diatur pada menu course.
                  </p>
                </div>
                <div v-if="using_zoom">
                  <p class="waktu-zoom">Waktu Zoom</p>
                  <v-row>
                    <v-col cols="4">
                      <v-text-field
                        v-model="zoom_time_hour"
                        label="Jam"
                        type="number"
                        required
                      ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        v-model="zoom_time_minute"
                        label="Menit"
                        type="number"
                        required
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </div>
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
                >Apakah kamu yakin untuk menghapus
                {{
                  selectedItem.item ? '"' + selectedItem.item + '"' : "item ini"
                }}
                ? Data lesson page juga akan ikut terhapus</v-toolbar
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
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import DOMPurify from "dompurify";
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
      isLoading: false,
      using_zoom: false,
      is_quiz: false,
      scope: null,
      is_journal_reflection: false,
      haveOptionZoom: false,
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      course_id: "",
      id: "",
      name: "",
      zoom_time_hour: 0,
      zoom_time_minute: 0,

      description: "",
      kkm: 0,
      have_kkm: false,
      max_attempt: 0,
      have_max_attempt: false,
      exam_id: "",

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
      kkmRules: [
        (v) => (v >= 0 && v <= 100) || "KKM must be between 0 and 100",
      ],
      max_attemptRules: [(v) => v > 0 || "Max Attempt must be greater than 0"],
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
          text: "Tipe",
          value: "type",
          width: "20%",
        },
        {
          text: "Time",
          value: "learn_time",
          width: "20%",
        },
        {
          text: "Action",
          value: "action",
          width: "25%",
        },
      ],

      scopeList: [
        {
          key: "tpm",
          name: "Tugas Praktik Mandiri",
        },
        {
          key: "uk",
          name: "Unjuk Keterampilan",
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
    is_quiz: {
      handler() {
        if (this.is_quiz) {
          this.using_zoom = false;
        }
      },
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
    getLessonType(item) {
      if (item.using_zoom) {
        return "ZOOM";
      }

      if (item.is_quiz) {
        return "QUIZ";
      }

      if (item.is_quiz && item.is_journal_reflection) {
        return "JOURNAL REFLECTION";
      }

      return "CONTENT";
    },
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
            this.haveOptionZoom = !!this.dataParent.link_zoom;
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
          url: `/v1/admin/lesson?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&courses_id=${this.$route.params.id}`,
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

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.name = "";
        this.using_zoom = false;
        this.is_quiz = false;
        this.is_journal_reflection = false;
        this.scope = null;
        this.description = "";
        this.zoom_time_hour = 0;
        this.zoom_time_minute = 0;
        this.have_kkm = false;
        this.kkm = 0;
        this.description = "";
        this.have_max_attempt = false;
        this.max_attempt = 0;
        this.exam_id = "";
      } else {
        this.id = item.id;
        this.name = item.name;
        this.scope = item.scope;
        this.using_zoom = !!item.using_zoom;
        this.is_quiz = !!item.is_quiz;
        if (item.using_zoom) {
          this.zoom_time_hour = Math.floor(item.learn_time / 3600);
          this.zoom_time_minute = Math.floor(
            (item.learn_time - this.zoom_time_hour * 3600) / 60
          );
        }
        if (item.is_quiz) {
          this.have_kkm = !!item.exam.kkm;
          this.kkm = item.exam.kkm;
          this.description = item.exam.description ?? "";
          this.have_max_attempt = !!item.exam.max_attempt;
          this.max_attempt = item.exam.max_attempt;
          this.exam_id = item.exam.id;
          this.is_journal_reflection = item.is_journal_reflection;
        }
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    moveUp(item) {
      this.isLoading = true;
      var data = {
        id: item.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/lesson/moveup`,
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
          url: `/v1/admin/lesson/movedown`,
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

    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
    },

    submitExam(lesson_id) {
      const formData = {
        name: this.name,
        lesson_id: lesson_id,
        is_quiz: 1,
        description: this.description,
        max_attempt: this.have_max_attempt ? this.max_attempt : null,
        kkm: this.have_kkm ? this.kkm : null,
      };
      // console.log(formData);
      return this.$root.upload("post", "/v1/admin/exam/create", formData);
    },

    submit() {
      this.addLoading = true;
      const learnTime = this.zoom_time_hour * 3600 + this.zoom_time_minute * 60;
      var formData = {
        name: DOMPurify.sanitize(this.name), // Membersihkan nilai
        courses_id: this.course_id,
        using_zoom: this.using_zoom ? 1 : 0,
        learn_time: Math.floor(learnTime),
        is_quiz: this.is_quiz ? 1 : 0,
        is_journal_reflection: this.is_journal_reflection ? 1 : 0,
        scope: this.scope,
      };
      this.$root
        .upload("post", "/v1/admin/lesson/create", formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            if (this.is_quiz) {
              const lessonId = res.data.data.Lesson.id;
              this.submitExam(lessonId)
                .then(() => {
                  setTimeout(() => {
                    this.dialog = false;
                    this.getMainData();
                    this.addLoading = false;

                    this.alert(res.data.message, "success");
                  }, 1000);
                })
                .catch((e) => {
                  console.log(e);
                  this.addLoading = false;
                });
            } else {
              setTimeout(() => {
                this.dialog = false;
                this.getMainData();
                this.addLoading = false;

                this.alert(res.data.message, "success");
              }, 1000);
            }
            return;
          } else {
            if (res.data.message == "Error validation") {
              this.alert("Semua field wajib diisi", "danger");
            } else {
              this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
            }
            this.addLoading = false;
          }
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },

    updateExam() {
      this.addLoading = true;
      const formData = {
        name: this.name,
        lesson_id: this.id,
        description: this.description,
        max_attempt: this.have_max_attempt ? this.max_attempt : null,
        kkm: this.have_kkm ? this.kkm : null,
      };
      this.$root
        .upload("post", `/v1/admin/exam/update/${this.exam_id}`, formData)
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

    update() {
      const learnTime = this.zoom_time_hour * 3600 + this.zoom_time_minute * 60;
      var formData = {
        name: DOMPurify.sanitize(this.name), // Membersihkan nilai
        courses_id: this.course_id,
        using_zoom: this.using_zoom ? 1 : 0,
        is_quiz: this.is_quiz ? 1 : 0,
        is_journal_reflection: this.is_journal_reflection ? 1 : 0,
        scope: this.scope,
        learn_time: Math.floor(learnTime),
      };
      this.$root
        .upload("post", `/v1/admin/lesson/update/${this.id}`, formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            if (this.is_quiz) {
              this.updateExam().then(() => {
                setTimeout(() => {
                  this.dialog = false;
                  this.getMainData();
                  this.addLoading = false;

                  this.alert(res.data.message, "success");
                }, 1000);
              });
            } else {
              setTimeout(() => {
                this.dialog = false;
                this.getMainData();
                this.addLoading = false;

                this.alert(res.data.message, "success");
              }, 1000);
            }
          } else {
            this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
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
          url: `/v1/admin/lesson/delete/${this.id}`,
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

    seeExamPage(item) {
      this.$router.push({
        path: `/admin/course/${this.course_id}/lesson/${item.id}/exam/${item.exam.id}/page`,
      });
    },

    seeLessonPage(item) {
      if (item.scope) {
        this.$router.push({
          path: `/admin/course/${this.course_id}/lesson/${item.id}/page?scope=${item.scope}`,
        });
      } else {
        this.$router.push({
          path: `/admin/course/${this.course_id}/lesson/${item.id}/page`,
        });
      }
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

    getTime(time) {
      const hours = Math.floor(time / 3600);
      const minutes = Math.floor((time - hours * 3600) / 60);
      const seconds = time - hours * 3600 - minutes * 60;

      return `${hours} hour ${minutes} min ${seconds} sec`;
    },
  },
  mounted() {
    var course_id = this.$route.params.id;
    if (course_id) {
      this.course_id = course_id;
      this.getParentData(course_id, this.getMainData);
    }
    this.$root.cms_tab = "course";
  },
};
</script>
