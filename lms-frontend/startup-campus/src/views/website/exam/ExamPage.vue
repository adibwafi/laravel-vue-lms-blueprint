<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div v-if="isMobile" class="main-container">
      <Header />
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <div v-if="isReady && examPage">
        <div class="header-area">
          <div class="progress-area">
            <v-progress-linear
              :value="((examIdx + 1) / exams.length) * 100"
              color="#0056d2"
              background-color="#D8E3E7"
              rounded
            ></v-progress-linear>
          </div>
          <div class="time" v-if="!examDetail.is_quiz">
            {{ hours | twoDigits }}:{{ minutes | twoDigits }}:{{
              seconds | twoDigits
            }}
          </div>
        </div>

        <div class="content-area">
          <div class="titles" v-if="examPage">{{ examPage?.name }}</div>
          <!-- Content Image -->
          <div
            v-if="
              examPage && examPage.media_type === 'image' && examPage.media_link
            "
          >
            <div class="media">
              <ZoomableImage :src="`${storageBaseUrl}${examPage.media_link}`" />
            </div>
          </div>
          <!-- Content Video -->
          <div v-if="examPage && examPage.media_type === 'video'">
            <div class="media">
              <div class="iframe">
                <iframe
                  :src="`https://www.youtube.com/embed/` + examPage.media_link"
                  frameborder="0"
                  allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                ></iframe>
              </div>
            </div>
          </div>

          <div v-if="examPage && examPage.content_type === 'fill_blank'">
            <div class="content" v-html="examData.content"></div>
            <div class="choice-area">
              <a
                @click="cekFillBlank(i)"
                class="btn-choice"
                :class="{
                  checked: i.checked == 'true',
                }"
                v-for="(i, idx) in examFillblankChoices"
                :key="idx"
                >{{ i?.value }}</a
              >
            </div>
          </div>
          <div class="content" v-else v-html="examData.content"></div>
          <!-- Multiple Choice -->
          <div v-if="examPage && examPage.content_type === 'multiple_choice'">
            <div class="choice-area">
              <a
                @click="cekChoice(i)"
                class="btn-choice"
                :class="{
                  checked: i.selected == 'true',
                }"
                v-for="(i, idx) in examChoices"
                :key="idx"
                >{{ i.answer }}
              </a>
            </div>
            <div v-if="haveMultipleAnswer">
              <p>* Jawaban bisa lebih dari satu</p>
            </div>
          </div>
          <!-- Answer -->
          <div v-else-if="examPage && examPage.content_type === 'answer'">
            <div class="answer-area f-form">
              <div class="f-label">Your answer</div>
              <v-textarea
                outlined
                dense
                placeholder="Tulis disini"
                background-color="#fff"
                v-model="answer"
              ></v-textarea>
            </div>
          </div>
        </div>
        <div class="cta-area" v-if="isAnswer || answer">
          <a @click="nextExam()" class="btn btn-blue btn-block">Continue</a>
        </div>
      </div>
    </div>
    <div v-else>
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <v-row>
        <!-- Main Content for Desktop -->
        <v-col cols="12" class="main-content">
          <NavbarFullDesktop
            v-if="examPage?.name"
            :title="`${examPage?.name}`"
          />
          <div v-if="isReady && examPage" class="content-container">
            <div class="header">
              <div class="header-area">
                <div class="progress-area">
                  <v-progress-linear
                    :value="((examIdx + 1) / exams.length) * 100"
                    color="#0056d2"
                    background-color="#D8E3E7"
                    rounded
                  ></v-progress-linear>
                </div>
                <div class="time" v-if="!examDetail.is_quiz">
                  {{ hours | twoDigits }}:{{ minutes | twoDigits }}:{{
                    seconds | twoDigits
                  }}
                </div>
              </div>

              <div class="content-area">
                <!-- Content Image -->
                <div
                  v-if="
                    examPage &&
                    examPage.media_type === 'image' &&
                    examPage.media_link
                  "
                >
                  <div class="media">
                    <ZoomableImage
                      :src="`${storageBaseUrl}${examPage.media_link}`"
                    />
                  </div>
                </div>
                <!-- Content Video -->
                <div v-if="examPage && examPage.media_type === 'video'">
                  <div class="media">
                    <div class="iframe">
                      <iframe
                        :src="
                          `https://www.youtube.com/embed/` + examPage.media_link
                        "
                        frameborder="0"
                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                      ></iframe>
                    </div>
                  </div>
                </div>

                <div v-if="examPage && examPage.content_type === 'fill_blank'">
                  <div class="content" v-html="examData.content"></div>
                  <div class="choice-area">
                    <a
                      @click="cekFillBlank(i)"
                      class="btn-choice"
                      :class="{
                        checked: i.checked == 'true',
                      }"
                      v-for="(i, idx) in examFillblankChoices"
                      :key="idx"
                      >{{ i?.value }}</a
                    >
                  </div>
                </div>
                <div class="content" v-else v-html="examData.content"></div>
                <!-- Multiple Choice -->
                <div
                  v-if="examPage && examPage.content_type === 'multiple_choice'"
                >
                  <div class="choice-area">
                    <a
                      @click="cekChoice(i)"
                      class="btn-choice"
                      :class="{
                        checked: i.selected == 'true',
                      }"
                      v-for="(i, idx) in examChoices"
                      :key="idx"
                      >{{ i.answer }}
                    </a>
                  </div>
                  <div v-if="haveMultipleAnswer">
                    <p>* Jawaban bisa lebih dari satu</p>
                  </div>
                </div>
                <!-- Answer -->
                <div v-else-if="examPage && examPage.content_type === 'answer'">
                  <div class="answer-area f-form">
                    <div class="f-label">Your answer</div>
                    <v-textarea
                      outlined
                      dense
                      placeholder="Tulis disini"
                      background-color="#fff"
                      v-model="answer"
                    ></v-textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="cta-area" v-if="isAnswer || answer">
              <a @click="nextExam()" class="btn btn-blue btn-block">Continue</a>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-fade-transition>
</template>

<script>
import moment from "moment";
import Header from "@/components/HeaderApp";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import ZoomableImage from "@/components/ZoomableImage.vue";
import { isMobile } from "@/helpers/deviceDetect";
import NavbarFullDesktop from "@/components/NavbarFullDesktop.vue";
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
    Loading,
    Alert,
    Header,
    ZoomableImage,
    NavbarFullDesktop,
  },
  data() {
    return {
      // localStorage
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      isMobile: false,
      userId: "",
      savedAnswerExamParsed: {},
      lastIndexParsed: {},

      poin: 1,
      userAnswerFillblankIdx: 0,

      course_id: "",
      examDetail: {},
      id: "",
      examPage: {},
      exams: [],
      examIdx: 0,
      banner: null,
      examData: {},
      examTooltips: [],
      examFillblank: [],
      examFillblankChoices: [],
      examFillblankUserAnswer: [],
      examChoices: [],
      examAnswer: "",
      answer: "",
      scores: {},
      recentPoin: "",
      idPoin: "",
      randomSeed: "",
      questionBankId: "",
      haveMultipleAnswer: false,

      btnAnswer: true,
      isAnswer: false,
      alertTrue: false,
      alertFalse: false,

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: true,

      type: "",

      // Countdown
      day: "",
      hour: "",
      min: "",
      sec: "",
      isCount: true,
      now: Math.trunc(new Date().getTime() / 1000),
      endTime: "",
      isFinish: false,
    };
  },
  methods: {
    cekChoice(item) {
      if (item.selected === "true") {
        // If the current option is already selected, deselect it
        this.$set(item, "selected", "false");
      } else {
        // If the current option is not selected, deselect all other options and select this one
        this.examChoices.forEach((choice) => {
          this.$set(choice, "selected", "false");
        });
        this.$set(item, "selected", "true");
      }

      this.poin = 1;
      const correctChoice = this.examChoices.filter((val) => val.checked);
      const selectedChoice = this.examChoices.filter(
        (val) => val.selected === "true"
      );
      if (correctChoice.length !== selectedChoice.length) {
        this.poin = 0;
      } else {
        selectedChoice.forEach((val) => {
          if (!val.checked) {
            this.poin = 0;
          }
        });
      }

      this.isAnswer = true;
    },
    cekFillBlank(item) {
      item.checked = "true";

      this.examData.content = this.examData.content.replace(
        `<span class="fill none">${
          this.examFillblank[this.userAnswerFillblankIdx].value
        }</span>`,
        `<span class="fill true">${item.value}</span>`
      );

      let tmpFillBlank = this.examFillblank[this.userAnswerFillblankIdx];
      if (tmpFillBlank.key != item.key) {
        this.poin = 0;
      }

      this.examFillblankUserAnswer.push({
        correct_answer_key: tmpFillBlank.key,
        correct_answer: tmpFillBlank.value,
        user_answer_key: item.key,
        user_answer: item.value,
      });

      if (this.userAnswerFillblankIdx + 1 >= this.examFillblank.length) {
        this.isAnswer = true;
        return;
      }

      this.userAnswerFillblankIdx++;
    },
    cekAnswer() {
      this.btnAnswer = false;
      var keyword = this.examData.keyword.split(",");
      let isTrue = true;
      this.answer = this.answer.toLowerCase().trim();
      for (var y = 0; y < keyword.length; y++) {
        if (!this.answer.includes(keyword[y].trim().toLowerCase())) {
          isTrue = false;
        }
      }
      if (!isTrue) {
        this.poin = 0;
      } else {
        this.poin = 1;
      }
      this.isAnswer = true;
    },
    shuffle(array) {
      let currentIndex = array.length,
        randomIndex;

      // While there remain elements to shuffle.
      while (currentIndex != 0) {
        // Pick a remaining element.
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;

        // And swap it with the current element.
        [array[currentIndex], array[randomIndex]] = [
          array[randomIndex],
          array[currentIndex],
        ];
      }

      return array;
    },
    nextExam() {
      if (this.examPage.content_type == "answer") {
        this.cekAnswer();
      }
      this.isLoading = true;
      this.getScore(this.$route.params.id)
        .then(() => {
          this.submitAnswer()
            .then(() => {
              if (this.examIdx < this.exams.length - 1) {
                var totalPoin = this.recentPoin + this.poin;
                this.submitPoin(totalPoin, false).then(() => {
                  this.examIdx += 1;
                  this.lastIndexParsed[this.id] = this.examIdx;
                  this.examPage = this.exams[this.examIdx];
                  this.examData = JSON.parse(this.examPage.data);
                  this.examTooltips = JSON.parse(this.examPage.tooltips);
                  if (this.examTooltips.length > 0) {
                    for (var i = 0; i < this.examTooltips.length; i++) {
                      this.examData.content = this.examData.content.replace(
                        this.examTooltips[i].key,
                        `<span class="hover-text">${this.examTooltips[i].value}
                    <span class="tooltip-text" id="bottom">${this.examTooltips[i].message}</span>
                  </span>`
                      );
                    }
                  }
                  if (this.examData.answer) {
                    this.examFillblank = this.examData.answer.slice();
                    this.examFillblankChoices = this.shuffle(
                      this.examData.answer.slice()
                    );
                    if (this.examFillblank.length > 0) {
                      for (var j = 0; j < this.examFillblank.length; j++) {
                        this.examFillblank[j].checked = this.examFillblank[j]
                          .checked
                          ? this.examFillblank[j].checked
                          : "none";
                        this.examFillblankChoices[j].checked = this
                          .examFillblankChoices[j].checked
                          ? this.examFillblankChoices[j].checked
                          : "none";
                        this.examData.content = this.examData.content.replace(
                          this.examFillblank[j].key,
                          `<span class="fill ${this.examFillblank[j].checked}">${this.examFillblank[j].value}</span>`
                        );
                      }
                    }
                  }
                  if (this.examData.choices) {
                    this.examChoices = this.examData.choices;
                    const correctChoice = this.examChoices.filter(
                      (val) => val.checked
                    );
                    if (correctChoice.length > 1) {
                      this.haveMultipleAnswer = true;
                    } else {
                      this.haveMultipleAnswer = false;
                    }
                  }
                  if (this.examData.keyword) {
                    this.examAnswer = this.examData.keyword;
                  }
                  // Reset
                  this.answer = "";
                  this.examFillblankUserAnswer = [];
                });
              } else {
                var Poin = this.recentPoin + this.poin;
                // Batasi nilai poin maksimum menjadi 100
                if (Poin > 100) {
                  Poin = 100;
                }
                this.submitPoin(Poin, true);
              }
              this.isLoading = false;
              this.poin = 1;
              this.userAnswerFillblankIdx = 0;
            })
            .catch((e) => {
              console.log(e);
            })
            .finally(() => {
              this.isLoading = false;
            });
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    alert(msg, alert) {
      if (alert == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = msg;
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
    getExamDetail(id) {
      this.loadScreen = true;
      this.isLoading = true;
      return this.$root
        .axios({
          method: "get",
          url: `/v1/exam/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.examDetail = res.data.data.exam;
            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
          console.log(e);
        })
        .finally(() => {
          this.loadScreen = false;
        });
    },
    getExamPages(id) {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam-page?limit=999&sortBy=sorter&orderBy=asc&exam_id=${id}&random_seed=${this.randomSeed}}&question_bank_id=${this.questionBankId}`,
        })
        .then((res) => {
          if (res?.data && res?.data?.success && res?.data?.data) {
            const examData = res?.data?.data?.exam;

            if (examData && examData?.rows && examData?.rows?.length) {
              this.isReady = true;
              const examRows = examData?.rows;
              this.exams = examRows;

              if (this.examIdx >= this.exams.length) {
                this.examIdx = this.exams.length - 1;
              }

              this.examPage = this.exams[this.examIdx];

              if (
                this.examPage &&
                this.examPage?.data &&
                this.examPage?.tooltips
              ) {
                this.examData = this.examPage?.data
                  ? JSON.parse(this.examPage.data)
                  : null;
                this.examTooltips = this.examPage?.tooltips
                  ? JSON.parse(this.examPage.tooltips)
                  : [];

                if (this.examTooltips?.length > 0) {
                  for (var i = 0; i < this.examTooltips?.length; i++) {
                    this.examData.content = this.examData.content.replace(
                      this.examTooltips[i]?.key,
                      `<span class="hover-text">${this.examTooltips[i].value}
                <span class="tooltip-text" id="bottom">${this.examTooltips[i].message}</span>
              </span>`
                    );
                  }
                }

                if (this.examData.answer) {
                  this.examFillblank = this.examData.answer.slice();
                  this.examFillblankChoices = this.shuffle(
                    this.examData.answer.slice()
                  );
                  if (this.savedAnswerExamParsed[this.examPage.id]) {
                    this.examFillblank =
                      this.savedAnswerExamParsed[
                        this.examPage.id
                      ].examFillblank;
                    this.examFillblankChoices =
                      this.savedAnswerExamParsed[
                        this.examPage.id
                      ].examFillblankChoices;
                  }
                  if (this.examFillblank.length > 0) {
                    for (var j = 0; j < this.examFillblank.length; j++) {
                      this.examFillblank[j].checked = this.examFillblank[j]
                        .checked
                        ? this.examFillblank[j].checked
                        : "none";
                      this.examFillblankChoices[j].checked = this
                        .examFillblankChoices[j].checked
                        ? this.examFillblankChoices[j].checked
                        : "none";
                      this.examData.content = this.examData.content.replace(
                        this.examFillblank[j].key,
                        `<span class="fill ${this.examFillblank[j].checked}">${this.examFillblank[j].value}</span>`
                      );
                    }
                  }
                }
                if (this.examData.choices) {
                  this.examChoices = this.examData.choices;
                  const correctChoice = this.examChoices.filter(
                    (val) => val.checked
                  );
                  if (correctChoice.length > 1) {
                    this.haveMultipleAnswer = true;
                  } else {
                    this.haveMultipleAnswer = false;
                  }
                }
                if (this.examData.keyword) {
                  this.examAnswer = this.examData.keyword;
                }
                // Reset
                this.answer = "";
                this.examFillblankUserAnswer = [];
              } else {
                console.error(
                  "Missing 'data' or 'tooltips' property in exam page:",
                  this.examPage
                );
                this.alert(
                  "Invalid response format or missing 'data' or 'tooltips' property in exam page",
                  "danger"
                );
              }
            }
          }
        })
        .catch((e) => {
          console.error("Error in getExamPages:", e);
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    getScore(id) {
      this.loadScreen = true;
      this.isLoading = true;

      return this.$root
        .axios({
          method: "get",
          url: `/v1/exam-poin/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.scores = res.data.data.exam;

            // Batasi nilai poin maksimal menjadi 100
            if (this.scores.poin > 100) {
              this.scores.poin = 100;
            }

            this.endTime =
              this.scores && this.scores.time_finish
                ? this.scores.time_finish
                : "";
            this.randomSeed = this.scores.random_seed;
            this.questionBankId = this.scores.question_bank_id
              ? this.scores.question_bank_id
              : "";

            this.recentPoin = parseFloat(this.scores.poin);
            this.idPoin = this.scores.id;
            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.error("Error in getScore:", e);
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
        })
        .finally(() => {
          this.loadScreen = false;
        });
    },
    getLastAnswer() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam-answer/last-answer/${this.idPoin}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.examIdx = res.data.data.ExamAnswer.length;
            return;
          }
        })
        .catch((e) => {
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
          console.log(e);
        });
    },
    submitAnswer() {
      let dataAnswer = "";
      switch (this.examPage.content_type) {
        case "fill_blank":
          dataAnswer = JSON.stringify(this.examFillblankUserAnswer);
          break;
        case "multiple_choice":
          dataAnswer = JSON.stringify(this.examChoices);
          break;
        case "answer":
          dataAnswer = JSON.stringify({
            user_answer: this.answer,
            correct_answer: this.examData.keyword
              .split(",")
              .map((k) => k.trim().toLowerCase()),
          });
          break;
        default:
          break;
      }
      var data = {
        exam_poin_id: this.idPoin,
        exam_page_id: this.examPage.id,
        data_answer: dataAnswer,
      };
      return this.$root
        .axios({
          method: "post",
          url: `/v1/exam-answer/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) return;
        })
        .catch((e) => {
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
          console.log(e);
          throw e;
        });
    },
    submitPoin(poin, finish) {
      // Batasi nilai poin maksimum menjadi 100
      if (poin > 100) {
        poin = 100;
      }
      var data = {
        poin: poin,
      };
      if (finish) {
        data = {
          poin: poin,
          finished_at: moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
        };
      }
      return this.$root
        .axios({
          method: "post",
          url: `/v1/exam-poin/update/${this.idPoin}`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            if (finish) {
              this.$router.push(
                `/topic/${this.$route.params.id_topic}/exam/${this.$route.params.id}/score/${this.idPoin}`
              );
            }
          }
        })
        .catch((e) => {
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
          console.log(e);
          throw e;
        });
    },
    submitCerificate(finish) {
      var data = {
        course_id: this.course_id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/certificate/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            if (finish) {
              this.$router.push(
                `/topic/${this.$route.params.id_topic}/exam/${this.$route.params.id}/score/${this.idPoin}`
              );
            }
          }
        })
        .catch((e) => {
          if (e.code === "ERR_NETWORK") {
            this.alert("Koneksi internet bermasalah", "danger");
          }
          console.log(e);
        });
    },
  },
  async mounted() {
    if (this.$root.token()) {
      this.isMobile = isMobile();
      var id = this.$route.params.id;
      this.course_id = this.$route.params.id_topic;
      var type = this.$route.query.type;
      if (id) {
        this.id = id;
        await this.getScore(id);
        this.userId = this.$root.userId();
        await this.getLastAnswer();

        const lastIndex = window.localStorage.getItem(
          `lastexamPageIndex-${this.userId}`
        );
        this.lastIndexParsed = lastIndex ? JSON.parse(lastIndex) : {};
        this.examIdx = this.lastIndexParsed[this.id] || 0;
        this.examIdx = parseInt(this.examIdx);

        window.localStorage.setItem(
          `lastPlayedExam-${this.userId}-${this.course_id}`,
          this.id
        );
      }
      if (type) {
        this.type = type;
      }

      await this.getExamPages(id);
      await this.getExamDetail(id);

      if (!this.examDetail.is_quiz && this.endTime) {
        window.setInterval(() => {
          // console.log("countdown");
          this.now = Math.trunc(new Date().getTime() / 1000);
          if (!this.isFinish && this.calculatedDate - this.now <= 0) {
            this.isFinish = true;
            this.submitPoin(this.recentPoin, true);
          }
        }, 1000);
      }
    }
  },

  computed: {
    secondCount() {
      return this.calculatedDate - this.now;
    },
    calculatedDate() {
      return this.endTime ? Math.trunc(Date.parse(this.endTime) / 1000) : 0;
    },
    seconds() {
      if (this.secondCount < 0) return 0;
      return this.secondCount % 60;
    },
    minutes() {
      if (this.secondCount < 0) return 0;
      return Math.trunc(this.secondCount / 60) % 60;
    },
    hours() {
      if (this.secondCount < 0) return 0;
      return Math.trunc(this.secondCount / 60 / 60) % 24;
    },
    days() {
      if (this.secondCount < 0) return 0;
      return Math.trunc(this.secondCount / 60 / 60 / 24);
    },
  },
  filters: {
    twoDigits(value) {
      if (value.toString().length <= 1) {
        return "0" + value.toString();
      }
      return value.toString();
    },
  },
};
</script>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;
  position: relative;

  .btn-answer {
    margin-top: 24px;
  }

  .btn-giveup {
    display: block;
    padding: 17px 0px;
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 700;
    // min-width: 250px;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid #0056d2;
    background: #0056d2;
    color: #fff !important;
    border-radius: 15px;
    box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
  }

  .btn-tryagain {
    display: block;
    padding: 17px 0px;
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 700;
    // min-width: 250px;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid #0056d2;
    background: #fff;
    color: #0056d2;
    border-radius: 15px;
    box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
  }

  .content-area {
    padding-bottom: 5rem;

    .titles {
      font-weight: 700;
      font-size: 16px;
      color: $main-color;
      text-align: center;
      padding: 10px 0 40px;
    }

    .media {
      line-height: 0;
      margin-bottom: 32px;

      img {
        border-radius: 20px;
        width: 100%;
      }

      .iframe {
        position: relative;
        width: 100%;
        overflow: hidden;
        padding-top: 56.25%;

        iframe {
          position: absolute;
          top: 0;
          left: 0;
          bottom: 0;
          right: 0;
          width: 100%;
          height: 100%;
          border: none;
        }
      }
    }

    .content {
      font-size: 16px;

      .tooltip {
        font-weight: 700;
        text-decoration: underline;
        cursor: pointer;
      }

      .fill {
        font-weight: 700;
        cursor: pointer;
        position: relative;
      }

      .fill::before {
        position: absolute;
        background: #ffffff;
        box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
        border-radius: 5px;
        width: 100%;
        height: calc(100% + 2px);
        content: "";
      }

      .fill::after {
        position: absolute;
        background: #ffffff;
        box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
        border-radius: 5px;
        width: 100%;
        height: calc(100% + 2px);
        content: "";
      }
    }

    .choice-area {
      margin-top: 40px;

      .btn-choice {
        word-wrap: break-word;
        background: #ffffff;
        box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
        border-radius: 15px;
        padding: 19px 30px;
        display: block;
        color: #2b2c27 !important;
        margin-bottom: 24px;
        font-weight: 500;
        font-size: 14px;
      }

      .btn-choice.false {
        background: #ea5757;
        color: #fff !important;
      }

      .btn-choice.true {
        background: #57ea5d;
        color: #fff !important;
      }

      .btn-choice.checked {
        background: #fff;
        color: #0056d2 !important;
        border: 2px solid #0056d2;
      }
    }

    .answer-area {
      margin-top: 40px;

      .v-input__slot {
        border-radius: 15px !important;
      }
    }
  }

  .popup-answer-false {
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99;
    padding: 30px 20px 18px;
    background: #ffffff;
    box-shadow: 0px -16px 40px rgba(112, 144, 176, 0.2);
    border-radius: 25px 25px 0 0;

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .heading {
        display: flex;
        align-items: center;

        img {
          width: 24px;
        }

        span {
          font-weight: 700;
          font-size: 18px;
          color: #ea5757;
          padding-left: 12px;
        }
      }

      .close {
        line-height: 0;

        img {
          width: 15px;
        }
      }
    }

    p {
      padding-top: 24px;
    }
  }

  .popup-answer-true {
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99;
    padding: 30px 20px 18px;
    background: #ffffff;
    box-shadow: 0px -16px 40px rgba(112, 144, 176, 0.2);
    border-radius: 25px 25px 0 0;

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .heading {
        display: flex;
        align-items: center;

        img {
          width: 24px;
        }

        span {
          font-weight: 700;
          font-size: 18px;
          color: #57ea5d;
          padding-left: 12px;
        }
      }

      .close {
        line-height: 0;

        img {
          width: 15px;
        }
      }
    }

    p {
      padding-top: 24px;
    }

    .cta {
      padding-top: 5px;
    }
  }

  .cta-area {
    background: #f6f8fd;
    padding: 18px 20px;
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9;
  }

  .header-area {
    display: flex;
    align-items: center;
    padding: 0px 0 20px;

    .icon {
      line-height: 0;

      img {
        height: 18px;
      }
    }

    .progress-area {
      width: 100%;
      padding: 0 16px;
    }
  }
}

.content-container {
  display: flex;
  flex-direction: column;
  padding: 26px 244px 26px 244px;
  gap: 32px;
  height: 90vh;
  justify-content: space-between;

  .header {
    display: flex;
    flex-direction: column;
    // align-items: center;
    color: #0056d2;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: 150%;
    letter-spacing: -0.384px;
    gap: 8px;

    .titles {
      display: flex;
      margin: 0;
    }

    .description {
      display: flex;
      flex-direction: column;
      margin: 0;
      color: var(--Black-Scale-Black-700, #3d3d3d);
      font-size: 18px;
      font-style: normal;
      font-weight: 400;
      line-height: 180%;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
    }
  }

  .cta-area {
    padding: 18px 20px;
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9;
  }

  .btn-answer {
    margin-top: 24px;
  }

  .btn-giveup {
    display: block;
    padding: 17px 0px;
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 700;
    // min-width: 250px;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid #0056d2;
    background: #0056d2;
    color: #fff !important;
    border-radius: 15px;
    box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
  }

  .btn-tryagain {
    display: block;
    padding: 17px 0px;
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 700;
    // min-width: 250px;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid #0056d2;
    background: #fff;
    color: #0056d2;
    border-radius: 15px;
    box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
  }

  .content-area {
    padding-bottom: 5rem;
    align-items: center;

    .titles {
      font-weight: 700;
      font-size: 16px;
      color: $main-color;
      text-align: center;
      padding: 10px 0 40px;
    }

    .media {
      line-height: 0;
      margin-bottom: 32px;

      img {
        border-radius: 20px;
        width: 100%;
      }

      .iframe {
        position: relative;
        width: 100%;
        overflow: hidden;
        padding-top: 56.25%;

        iframe {
          position: absolute;
          top: 0;
          left: 0;
          bottom: 0;
          right: 0;
          width: 100%;
          height: 100%;
          border: none;
        }
      }
    }

    .content {
      font-size: 16px;

      .tooltip {
        font-weight: 700;
        text-decoration: underline;
        cursor: pointer;
      }

      .fill {
        font-weight: 700;
        cursor: pointer;
        position: relative;
      }

      .fill::before {
        position: absolute;
        background: #ffffff;
        box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
        border-radius: 5px;
        width: 100%;
        height: calc(100% + 2px);
        content: "";
      }

      .fill::after {
        position: absolute;
        background: #ffffff;
        box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
        border-radius: 5px;
        width: 100%;
        height: calc(100% + 2px);
        content: "";
      }
    }

    .choice-area {
      margin-top: 40px;

      .btn-choice {
        word-wrap: break-word;
        background: #ffffff;
        box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
        border-radius: 15px;
        padding: 19px 30px;
        display: block;
        color: #2b2c27 !important;
        margin-bottom: 24px;
        font-weight: 500;
        font-size: 14px;
      }

      .btn-choice.false {
        background: #ea5757;
        color: #fff !important;
      }

      .btn-choice.true {
        background: #57ea5d;
        color: #fff !important;
      }

      .btn-choice.checked {
        background: #fff;
        color: #0056d2 !important;
        border: 2px solid #0056d2;
      }
    }

    .answer-area {
      margin-top: 40px;

      .v-input__slot {
        border-radius: 15px !important;
      }
    }
  }

  .popup-answer-false {
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99;
    padding: 30px 20px 18px;
    background: #ffffff;
    box-shadow: 0px -16px 40px rgba(112, 144, 176, 0.2);
    border-radius: 25px 25px 0 0;

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .heading {
        display: flex;
        align-items: center;

        img {
          width: 24px;
        }

        span {
          font-weight: 700;
          font-size: 18px;
          color: #ea5757;
          padding-left: 12px;
        }
      }

      .close {
        line-height: 0;

        img {
          width: 15px;
        }
      }
    }

    p {
      padding-top: 24px;
    }
  }

  .popup-answer-true {
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99;
    padding: 30px 20px 18px;
    background: #ffffff;
    box-shadow: 0px -16px 40px rgba(112, 144, 176, 0.2);
    border-radius: 25px 25px 0 0;

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .heading {
        display: flex;
        align-items: center;

        img {
          width: 24px;
        }

        span {
          font-weight: 700;
          font-size: 18px;
          color: #57ea5d;
          padding-left: 12px;
        }
      }

      .close {
        line-height: 0;

        img {
          width: 15px;
        }
      }
    }

    p {
      padding-top: 24px;
    }

    .cta {
      padding-top: 5px;
    }
  }

  .header-area {
    display: flex;
    align-items: center;
    padding: 0px 0 20px;

    .icon {
      line-height: 0;

      img {
        height: 18px;
      }
    }

    .progress-area {
      width: 100%;
      padding: 0 16px;
    }
  }
}
</style>

<style lang="scss">
.answer-area {
  margin-top: 40px;

  .v-input__slot {
    border-radius: 15px !important;
  }
}

.v-tooltip__content.menuable__content__active {
  opacity: 1;
  margin: 0 20px;
  padding: 20px;
  display: block;
  width: calc(100% - 40px);
  left: 0 !important;
}

@media (min-width: 768px) {
  .v-tooltip__content.menuable__content__active {
    opacity: 1;
    margin: 0 0px;
    padding: 20px;
    width: calc(100% - 100px);
    display: inline-block;
    max-width: 414px;
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
}

.content {
  .fill {
    font-weight: 700;
    cursor: pointer;
    position: relative;
    display: inline-block;
  }

  .fill.true {
    color: #0056d2;
  }

  .fill.true::before,
  .fill.true::after {
    display: none;
  }

  .fill.false {
    color: #0056d2;
  }

  .fill.false::before,
  .fill.false::after {
    display: none;
  }

  .fill::before {
    position: absolute;
    background: #ffffff;
    box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
    border-radius: 5px;
    width: 100%;
    height: calc(100% + 2px);
    content: "";
    left: 0;
  }

  .fill::after {
    position: absolute;
    background: #ffffff;
    box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
    border-radius: 5px;
    width: 100%;
    height: calc(100% + 2px);
    content: "";
    left: 0;
  }

  p {
    margin-bottom: 0 !important;
  }
}

.tooltip-text {
  visibility: hidden;
  position: absolute;
  z-index: 2;
  width: 100%;
  max-width: 414px;
  color: white;
  font-size: 12px;
  background-color: #0056d2;
  padding: 30px;
}

// .tooltip-text::before {
//   content: "";
//   position: absolute;
//   transform: rotate(45deg);
//   background-color: #0056d2;
//   padding: 8px;
//   z-index: 1;
// }

.tooltip-text::before {
  content: "X";
  position: absolute;
  color: #fff;
  padding: 8px;
  z-index: 1;
}

.hover-text:hover .tooltip-text {
  visibility: visible;
}

.tooltip-text:hover {
  visibility: hidden !important;
}

#bottom {
  // top: 35px;
  left: 50%;
  transform: translateX(-50%);
  margin-top: 35px;
}

#bottom::before {
  top: 0px;
  right: 5px;
}

.hover-text {
  // position: relative;
  display: inline-block;
  font-weight: 700;
  text-decoration: underline;
  cursor: pointer;
}

.hover-text:hover {
  background: #0056d2;
  color: #fff;
  text-decoration: none;
}
</style>

<style>
.false > .v-input__control > .v-input__slot fieldset {
  background: #ea5757 !important;
}

.true > .v-input__control > .v-input__slot fieldset {
  background: #57ea5d !important;
}

.false > .v-input__control > .v-input__slot textarea {
  color: #fff !important;
}

.true > .v-input__control > .v-input__slot textarea {
  color: #fff !important;
}
</style>
