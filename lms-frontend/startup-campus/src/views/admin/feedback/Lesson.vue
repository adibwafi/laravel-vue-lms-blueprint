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
                <div class="active">Submission</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Submission</h2>
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
                    <div>{{ item.user?.fullname }}</div>
                  </template>
                  <template v-slot:[`item.lesson`]="{ item }">
                    <div>{{ item.lesson?.name }}</div>
                  </template>
                  <template v-slot:[`item.email`]="{ item }">
                    <div>{{ item.user?.email }}</div>
                  </template>
                  <template v-slot:[`item.scope`]="{ item }">
                    <div>{{ item.lesson?.scope }}</div>
                  </template>
                  <template v-slot:[`item.answer`]="{ item }">
                    <div>
                      <a :href="item.answer" target="_blank">Open Link</a>
                    </div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom v-if="item.notes">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="blue"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="openDialog(item)"
                        >
                          <v-icon dark> mdi-eye </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Feedback</span>
                    </v-tooltip>
                    <v-tooltip bottom v-else>
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
                      <span>Beri Feedback</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>

        <v-dialog
          v-model="dialogFeedback"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogFeedback">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Berikan Feedback {{ selectedItem.lesson?.name }}</v-toolbar
              >
              <v-card-text>
                <v-form class="mt-24">
                  <v-textarea
                    v-if="selectedItem?.notes"
                    v-model="selectedItem.notes"
                    label="Catatan"
                    placeholder="Masukkan Catatan"
                    required
                  ></v-textarea>
                  <v-textarea
                    v-else
                    v-model="notes"
                    label="Catatan"
                    placeholder="Masukkan Catatan"
                    required
                  ></v-textarea>
                </v-form>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitFeedbackCourse()"
                    :disabled="addLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{
                      addLoading
                        ? "Loading..."
                        : selectedItem.notes
                        ? "Update"
                        : "Submit"
                    }}
                  </button>
                  <a
                    @click="dialogFeedback = false"
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
import DOMPurify from "dompurify";
export default {
  name: "FeedbackLessonPage",
  metaInfo() {
    return {
      title: "Feedback Lesson - AcmeLMS",
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
      course_id: "",
      dialogFeedback: false,
      notes: "",

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
          width: "20%",
        },
        {
          text: "Email",
          value: "email",
          width: "20%",
        },
        {
          text: "Lessson",
          value: "lesson",
          width: "20%",
        },
        {
          text: "Scope",
          value: "scope",
          width: "10%",
        },
        {
          text: "Submission",
          value: "answer",
          width: "20%",
        },
        {
          text: "Action",
          value: "action",
          width: "10%",
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
          // console.log("INI Response", res);
          if (res.data.success) {
            this.dataParent = res.data.data.Course;
            return;
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
      // var sortBy = this.options.sortBy[0] ? this.options.sortBy[0] : "sorter";
      // var sortDesc = this.options.sortDesc[0]
      //   ? this.options.sortDesc[0]
      //   : "ASC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/user-lesson/answers?search=${search}&page=${page}&limit=${itemsPerPage}&courses_id=${this.$route.params.course_id}`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.datarow = res.data.data.UserLessonAnswer.rows;
            this.totalData = res.data.data.UserLessonAnswer.count;
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
      this.selectedItem = item;
      this.dialogFeedback = true;
    },

    submitFeedbackCourse() {
      this.addLoading = true;
      var formData = {
        redeem_code: this.selectedItem?.user_course_category?.redeem_code,
        sequence: this.selectedItem?.course?.order,
        scope: this.selectedItem?.lesson?.scope,
        notes: this.selectedItem?.notes ? this.selectedItem?.notes : this.notes,
      };
      this.$root
        .upload(
          "post",
          `/v1/admin/user-lesson/answers/${this.selectedItem.id}/feedbacks`,
          formData
        )
        .then((res) => {
          if (res.data.success) {
            this.alert("Feedback user course success", "success");
            this.dialogFeedback = false;
            this.getMainData();
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
