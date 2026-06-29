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
                <div class="active">Jawaban</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Tracking Jawaban Exam</h2>
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
                  :items-per-page="15"
                  item-key="id"
                  show-expand
                >
                  <template v-slot:expanded-item="{ headers, item }">
                    <!-- <td
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
                    </td> -->
                    <td
                      :colspan="headers.length"
                      v-if="item.content_type == 'multiple_choice'"
                      align="center"
                      class="py-8"
                    >
                      <v-card>
                        <v-card-title>Presentase Jawaban Peserta</v-card-title>
                        <v-simple-table>
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th class="text-left">Jawaban</th>
                                <th class="text-left">
                                  Jumlah peserta menjawab
                                </th>
                                <th class="text-left">
                                  Presentase Peserta Menjawab
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="(val, choice) in item.all_user_answer"
                                :class="{ success: val.correct_answer }"
                                :style="{
                                  textAlign: 'center',
                                }"
                                :key="choice"
                              >
                                <td>
                                  {{ choice }}
                                  {{ val.correct_answer ? " (Benar)" : "" }}
                                </td>
                                <td>{{ val.number_answer }}</td>
                                <td>{{ val.percentage_answer }} %</td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card>
                    </td>
                    <td
                      :colspan="headers.length"
                      v-else-if="item.content_type == 'answer'"
                      align="center"
                      class="py-8"
                    >
                      <v-card>
                        <v-card-title>Presentase Jawaban Peserta</v-card-title>
                        <v-simple-table>
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th class="text-center">Jawaban</th>
                                <th class="text-center">
                                  Jumlah Peserta Menjawab
                                </th>
                                <th class="text-center">
                                  Presentase Peserta Menjawab
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="(val, choice) in item.all_user_answer"
                                :class="{ success: val.correct_answer }"
                                :style="{
                                  textAlign: 'center',
                                }"
                                :key="choice"
                              >
                                <td>
                                  {{ choice }}
                                  {{ val.correct_answer ? " (Benar)" : "" }}
                                </td>
                                <td>{{ val.number_answer }}</td>
                                <td>{{ val.percentage_answer }} %</td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card>
                    </td>
                    <td
                      :colspan="headers.length"
                      v-else-if="item.content_type == 'fill_blank'"
                      align="center"
                      class="py-8"
                    >
                      <v-card>
                        <v-card-title>Presentase Jawaban Peserta</v-card-title>
                        <v-simple-table>
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th class="text-center">Keyword</th>
                                <th class="text-center">Jawaban</th>
                                <th class="text-center">
                                  Jumlah Peserta Benar
                                </th>
                                <th class="text-center">
                                  Presentase Peserta Benar
                                </th>
                                <th class="text-center">
                                  Presentase Peserta Salah
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="(val, choice) in item.all_user_answer"
                                :key="choice"
                                :style="{
                                  textAlign: 'center',
                                }"
                              >
                                <td>
                                  {{ choice }}
                                </td>
                                <td>
                                  {{ val.correct_answer }}
                                </td>
                                <td>{{ val.number_correct }}</td>
                                <td>{{ val.percentage_correct }} %</td>
                                <td>{{ 100 - val.percentage_correct }} %</td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card>
                    </td>
                  </template>
                  <template
                    v-slot:[`item.percentage_correct_answer`]="{ item }"
                  >
                    <v-progress-circular
                      :rotate="360"
                      :size="100"
                      :width="15"
                      :value="item.percentage_correct_answer"
                      :color="
                        getColorPercentageCorrectAnswer(
                          item.percentage_correct_answer
                        )
                      "
                    >
                      {{ item.percentage_correct_answer }}
                    </v-progress-circular>
                  </template>
                  <template v-slot:[`item.content`]="{ item }">
                    <div
                      class="mt-4 mb-4"
                      v-html="JSON.parse(item.data).content"
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
                  <template v-slot:[`item.content_type`]="{ item }">
                    <v-chip :color="getColor(item)" dark>{{
                      item.content_type
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
export default {
  name: "ExamAnswerPage",
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
      typingTimer: 0,
      selectedItem: {},
      expanded: [],

      headers: [
        {
          text: "Nama",
          value: "name",
          width: "10%",
        },
        {
          text: "Soal",
          value: "content",
          width: "10%",
        },
        {
          text: "Jawaban",
          value: "answer",
          width: "10%",
        },
        {
          text: "Persentase Benar",
          value: "percentage_correct_answer",
          width: "10%",
        },
        {
          text: "Tipe",
          value: "content_type",
          width: "10%",
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
    getColorPercentageCorrectAnswer(percentage) {
      if (percentage > 70) {
        return "green";
      } else if (percentage > 50) {
        return "orange";
      } else {
        return "red";
      }
    },
    getAnswer(item) {
      const data = JSON.parse(item.data);
      switch (item.content_type) {
        case "answer": {
          // console.log([data.keyword]);
          return [data.keyword];
        }
        case "fill_blank": {
          const answer = data.answer;
          const mapAnswer = answer.map((a) => `${a.key} -> ${a.value}`);
          return mapAnswer;
        }
        case "multiple_choice": {
          const answer = data.choices
            .filter((c) => c.checked)
            .map((c) => c.answer);
          return answer;
        }
        default:
          return "black";
      }
    },
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
          url: `/v1/admin/exam-page?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&exam_id=${this.examId}&withTracking=true`,
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
    this.examId = this.$route.params.exam_id;
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
