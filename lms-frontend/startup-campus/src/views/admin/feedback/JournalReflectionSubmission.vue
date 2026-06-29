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
                <!-- back page -->
                <a @click="$router.go(-1)">Back</a>
                <div class="active">Journal Reflection Submission</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Journal Reflection Submission</h2>
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
                        <a @click="openDialog(item)">
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
                        </a>
                      </template>
                      <span>Lihat Jawaban</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
      </div>

      <v-dialog
        v-model="dialog"
        transition="dialog-bottom-transition"
        max-width="800"
      >
        <v-card>
          <v-toolbar color="#09408e" dark>
            Jawaban {{ selectedItem?.fullname }}</v-toolbar
          >
          <v-card-text>
            <v-data-table
              :headers="headersJawaban"
              :items="selectedItem?.answers"
              loading-text="Loading... Please wait"
            >
              <template v-slot:[`item.question`]="{ item }">
                <div class="text-tbl">
                  {{ theString(item.question, "content") }}
                </div>
              </template>
              <template v-slot:[`item.data_answer`]="{ item }">
                <div
                  class="text-tbl"
                  v-html="theString(item.data_answer, 'user_answer')"
                ></div>
              </template>
            </v-data-table>
            <div class="btn-duo mt-6">
              <a @click="dialog = false" class="btn btn-blue btn-sm tf"
                >Tutup</a
              >
            </div>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>
  </div>
</template>

<style lang="scss">
.text-tbl {
  text-align: start !important;

  p {
    margin: 0 !important;
    padding: 0 !important;
  }
}
</style>

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
  name: "JournalReflectionSubmissionPage",
  metaInfo() {
    return {
      title: "Journal Reflection Submission - AcmeLMS",
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
      lesson_id: "",
      dialog: false,
      id: "",
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
          value: "fullname",
          align: "start",
          width: "30%",
        },
        {
          text: "Email",
          value: "email",
          width: "30%",
        },
        {
          text: "Action",
          value: "action",
          width: "25%",
        },
      ],

      headersJawaban: [
        {
          text: "Pertanyaan",
          value: "question",
          align: "start",
          width: "50%",
        },
        {
          text: "Jawaban",
          value: "data_answer",
          width: "50%",
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getMainData(this.$route.params.id);
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
    getMainData(id) {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/exam-answer/journal-reflection/${id}?search=${search}&page=${page}&limit=${itemsPerPage}`,
        })
        .then((res) => {
          console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.exam_answer.rows;
            this.totalData = res.data.data.exam_answer.count;
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
      this.textNotif = DOMPurify.sanitize(item); // Membersihkan nilai
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },

    openDialog(item) {
      this.selectedItem = item;
      this.dialog = true;
    },

    cleanString(text) {
      return text.replace(/<[^>]*>|&nbsp;/g, "");
    },

    theString(data, key) {
      var jsonObject = JSON.parse(data);
      return this.cleanString(jsonObject[key]);
    },
  },
  mounted() {
    var lesson_id = this.$route.params.id;
    if (lesson_id) {
      this.lesson_id = lesson_id;
      this.getMainData(lesson_id);
    }
    this.$root.cms_tab = "tracking/feedback";
  },
};
</script>
