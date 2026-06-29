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
                <h2>Users list</h2>
                <!-- <button class="btn btn-blue btn-sm" @click="openDialog()">
                  Add New
                </button> -->
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
                  item-key="id"
                  show-expand
                >
                  <template v-slot:expanded-item="{ headers, item }">
                    <td
                      :colspan="headers.length"
                      v-if="item.loading"
                      align="center"
                      class="py-8"
                    >
                      <v-progress-circular
                        :size="50"
                        :width="5"
                        color="blue"
                        indeterminate
                      ></v-progress-circular>
                    </td>
                    <td :colspan="headers.length" v-else>
                      <v-row class="detail-row">
                        <v-col cols="12">
                          <v-card>
                            <v-card-title>
                              Course Category yang diikuti oleh
                              {{ item.fullname }}
                            </v-card-title>
                            <v-simple-table>
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-left">Nomor</th>
                                    <th class="text-left">Nama</th>
                                    <th class="text-left">Selesai</th>
                                    <th class="text-left">Progress</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(
                                      ucc, index
                                    ) in item.user_course_category"
                                    :key="ucc.id"
                                  >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ ucc.course_category.name }}</td>
                                    <td>
                                      <v-chip
                                        v-if="ucc.user_complete"
                                        color="green"
                                        dark
                                      >
                                        Sudah
                                      </v-chip>
                                      <v-chip color="red" dark v-else>
                                        Belum
                                      </v-chip>
                                    </td>
                                    <td>
                                      {{ ucc.user_progress_percentage }} %
                                    </td>
                                  </tr>
                                </tbody>
                              </template>
                            </v-simple-table>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row class="detail-row">
                        <v-col cols="12">
                          <v-card>
                            <v-card-title>
                              Course yang diikuti oleh
                              {{ item.fullname }}
                            </v-card-title>
                            <v-simple-table>
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-left">Nomor</th>
                                    <th class="text-left">Nama</th>
                                    <th class="text-left">Selesai</th>
                                    <th class="text-left">Progress</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(uc, index) in item.user_course"
                                    :key="uc.id"
                                  >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ uc.course.name }}</td>
                                    <td>
                                      <v-chip
                                        v-if="uc.user_complete"
                                        color="green"
                                        dark
                                      >
                                        Sudah
                                      </v-chip>
                                      <v-chip color="red" dark v-else>
                                        Belum
                                      </v-chip>
                                    </td>
                                    <td>{{ uc.user_progress_percentage }} %</td>
                                  </tr>
                                </tbody>
                              </template>
                            </v-simple-table>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row class="detail-row">
                        <v-col cols="12">
                          <v-card>
                            <v-card-title>
                              Exam yang diikuti oleh
                              {{ item.fullname }}
                            </v-card-title>
                            <v-simple-table>
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-left">Nomor</th>
                                    <th class="text-left">Course Category</th>
                                    <th class="text-left">Course</th>
                                    <th class="text-left">Nama</th>
                                    <th class="text-left">Nilai</th>
                                    <th class="text-left">Di atas KKM</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(epe, index) in item.exam_poin_exam"
                                    :key="epe.id"
                                  >
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                      {{
                                        epe.exam?.course?.course_category?.name
                                      }}
                                    </td>
                                    <td>
                                      {{ epe.exam?.course?.name }}
                                    </td>
                                    <td>{{ epe.exam?.name }}</td>
                                    <td>
                                      {{ epe.score }}
                                    </td>
                                    <td>
                                      <v-chip
                                        v-if="epe.score > epe.exam?.kkm"
                                        color="green"
                                        dark
                                      >
                                        Ya
                                      </v-chip>
                                      <v-chip color="red" v-else dark>
                                        Tidak
                                      </v-chip>
                                    </td>
                                  </tr>
                                </tbody>
                              </template>
                            </v-simple-table>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row class="detail-row">
                        <v-col cols="12">
                          <v-card>
                            <v-card-title>
                              Quiz yang diikuti oleh
                              {{ item.fullname }}
                            </v-card-title>
                            <v-simple-table>
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-left">Nomor</th>
                                    <th class="text-left">Course Category</th>
                                    <th class="text-left">Course</th>
                                    <th class="text-left">Lesson</th>
                                    <th class="text-left">Nilai</th>
                                    <th class="text-left">Di atas KKM</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(epq, index) in item.exam_poin_quiz"
                                    :key="epq.id"
                                  >
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                      {{
                                        epq.exam?.lesson?.course
                                          ?.course_category?.name
                                      }}
                                    </td>
                                    <td>
                                      {{ epq.exam?.lesson?.course?.name }}
                                    </td>
                                    <td>
                                      {{ epq.exam?.lesson?.name }}
                                    </td>
                                    <td>
                                      {{ epq.score }}
                                    </td>
                                    <td>
                                      <v-chip
                                        v-if="epq.score > epq.exam?.kkm"
                                        color="green"
                                        dark
                                      >
                                        Ya
                                      </v-chip>
                                      <v-chip color="red" v-else dark>
                                        Tidak
                                      </v-chip>
                                    </td>
                                  </tr>
                                </tbody>
                              </template>
                            </v-simple-table>
                          </v-card>
                        </v-col>
                      </v-row>
                    </td>
                  </template>
                  <template v-slot:[`item.image`]="{ item }">
                    <div class="dataimg">
                      <img
                        v-if="item.image"
                        :src="`${storageBaseUrl}` + item.image"
                        alt=""
                        class="imgs"
                      />
                      <img
                        src="@/assets/img/img-placeholder.jpeg"
                        alt=""
                        v-else
                      />
                    </div>
                  </template>
                  <template
                    v-slot:[`item.tracking.average_exam_score`]="{ item }"
                  >
                    <div>
                      {{
                        item.tracking.average_exam_score
                          ? parseFloat(
                              item.tracking.average_exam_score
                            ).toFixed(2)
                          : "-"
                      }}
                    </div>
                  </template>
                  <template
                    v-slot:[`item.tracking.average_quiz_score`]="{ item }"
                  >
                    <div>
                      {{
                        item.tracking.average_quiz_score
                          ? parseFloat(
                              item.tracking.average_quiz_score
                            ).toFixed(2)
                          : "-"
                      }}
                    </div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="primary"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="seeCourses(item)"
                        >
                          <v-icon dark> mdi-text-account </v-icon>
                        </v-btn>
                      </template>
                      <span>User Courses</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-if="item.is_blocked == '1'"
                          class="mrb-8"
                          color="teal"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="unblockUser(item)"
                        >
                          <v-icon dark> mdi-account-lock-open </v-icon>
                        </v-btn>
                      </template>
                      <span>Unblock User</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
        <!-- Modal Block / Unblock item -->
        <v-dialog
          v-model="dialogBlock"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogBlock">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to block
                {{
                  selectedItem.fullname
                    ? '"' + selectedItem.fullname + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitBlock()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Block" }}
                  </button>
                  <a
                    @click="dialogBlock.value = false"
                    class="btn btn-text btn-sm tf"
                    >Cancel</a
                  >
                </div>
              </v-card-text>
            </v-card>
          </template>
        </v-dialog>
        <v-dialog
          v-model="dialogUnblock"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogUnblock">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to unblock
                {{
                  selectedItem.fullname
                    ? '"' + selectedItem.fullname + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitUnblock()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Unblock" }}
                  </button>
                  <a
                    @click="dialogUnblock.value = false"
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
import DOMPurify from "dompurify";
export default {
  name: "UserPage",
  metaInfo() {
    return {
      title: "User - AcmeLMS",
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
      expanded: [],
      isLoading: false,
      addLoading: false,
      dialog: false,
      dialogBlock: false,
      dialogUnblock: false,
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
      options: {
        itemsPerPage: 5,
      },
      typingTimer: 0,
      selectedItem: {},

      headers: [
        {
          text: "Image",
          align: "start",
          value: "image",
          width: "10%",
        },
        {
          text: "Fullname",
          value: "fullname",
          width: "10%",
        },
        {
          text: "Email",
          value: "email",
          width: "5%",
        },
        {
          text: "Phone",
          value: "phone",
          width: "5%",
        },
        {
          text: "Course Category Diikuti",
          value: "tracking.number_course_category_enrolled",
          width: "5%",
        },
        {
          text: "Course Diikuti",
          value: "tracking.number_course_enrolled",
          width: "5%",
        },
        {
          text: "Lesson Selesai",
          value: "tracking.number_lesson_completed",
          width: "5%",
        },
        {
          text: "Exam Selesai",
          value: "tracking.number_exam_completed",
          width: "5%",
        },
        {
          text: "Kuis Selesai",
          value: "tracking.number_quiz_completed",
          width: "5%",
        },
        {
          text: "Rata-Rata Nilai Kuis",
          value: "tracking.average_quiz_score",
          width: "5%",
        },
        {
          text: "Rata-Rata Nilai Exam",
          value: "tracking.average_exam_score",
          width: "5%",
        },
        { text: "Detail", value: "data-table-expand", width: "5%" },
      ],
    };
  },
  watch: {
    expanded: {
      async handler() {
        const expandedIds = this.expanded.map((item) => item.id);
        expandedIds.forEach(async (id) => {
          const index = this.datarow.findIndex((item) => item.id === id);
          if (this.datarow[index].have_detail) {
            return;
          }
          this.datarow[index].loading = true;
          const response = await this.getDetailUser(id);
          const user = response.data.data.user;
          const dataRowCopy = [...this.datarow];
          dataRowCopy[index] = user;
          this.datarow = dataRowCopy;
          this.datarow[index].have_detail = true;
          this.datarow[index].loading = false;
        });
      },
      deep: true,
    },
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
    getDetailUser(userId) {
      return this.$root.axios({
        method: "get",
        url: `/v1/admin/user/${userId}?with_tracking_detail=true&with_tracking=true`,
      });
    },
    getDate(dateStr) {
      try {
        if (!dateStr) {
          return "-";
        }

        new Date(dateStr).toLocaleString("id-ID") + " WIB";
      } catch (error) {
        return "-";
      }
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
          url: `/v1/admin/user?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&with_tracking=true`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.user.rows;
            // console.log(
            //   "🚀 ~ file: User.vue:350 ~ .then ~ datarow",
            //   this.datarow
            // );

            this.totalData = res.data.data.user.count;
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

    seeCourses(item) {
      this.$router.push({
        path: `/admin/users/${item.id}/courses`,
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
    this.getMainData();
    this.$root.cms_tab = "default";
  },
};
</script>

<style lang="scss" scoped>
.detail-row {
  margin: 1rem 0rem;
  width: 100%;
}

.v-card__title {
  font-size: 1rem !important;
}
</style>
