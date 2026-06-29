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
      <div v-if="isReady">
        <div class="header-area">
          <a @click="backLesson()">
            <div class="icon">
              <img src="@/assets/img/bar-back.png" alt="" />
            </div>
          </a>
          <div class="progress-area">
            <v-progress-linear
              :value="((lessonIdx + 1) / lessons.length) * 100"
              color="#0056d2"
              background-color="#D8E3E7"
              rounded
            ></v-progress-linear>
          </div>
          <a @click="$router.go(-1)">
            <div class="icon">
              <v-icon color="#0056D2">mdi-close-circle</v-icon>
            </div>
          </a>
        </div>
        <div class="content-area">
          <div class="titles">{{ lessonPage.name }}</div>
          <!-- Content Image -->
          <div v-if="lessonPage.media_type == 'image'">
            <div class="media">
              <ZoomableImage
                :src="`${storageBaseUrl}${lessonPage.media_link}`"
                width="100%"
                height="100%"
              />
            </div>
          </div>
          <!-- Content Pdf -->
          <div v-else-if="lessonPage.media_type == 'pdf'">
            <PdfViewer
              :source="`${storageBaseUrl}${lessonPage.media_link}`"
            ></PdfViewer>
          </div>
          <!-- Content Video -->
          <div v-if="lessonPage.media_type == 'video'">
            <div class="media">
              <div class="iframe">
                <iframe
                  :src="
                    `https://www.youtube.com/embed/` + lessonPage.media_link
                  "
                  frameborder="0"
                  allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                ></iframe>
              </div>
            </div>
          </div>

          <div v-if="lessonPage.content_type == 'fill_blank'">
            <!-- <div class="content" v-html="lessonData.content"></div> -->
            <ContentSection :content="lessonData.content" />
            <div class="choice-area">
              <a
                @click="cekFillBlank(i)"
                class="btn-choice"
                :class="{
                  true: i.checked == 'true',
                  false: i.checked == 'false',
                }"
                v-for="(i, idx) in lessonFillblankChoices"
                :key="idx"
                >{{ i.value }}</a
              >
            </div>
          </div>
          <!-- <div class="content" v-else v-html="lessonData.content"></div> -->
          <template v-if="lessonData.content">
            <ContentSection :content="lessonData.content" />
            <div
              v-if="
                prakerjaData?.scope &&
                prakerjaData?.answer &&
                prakerjaData?.is_completed == 'true'
              "
              class="mt-4"
            >
              <span
                style="
                  font-family: 'Sora', sans-serif;
                  font-size: 16px;
                  font-weight: 700;
                "
                >Link Submission:</span
              >
              <div>
                <a :href="prakerjaData?.answer" target="_blank">Buka link</a>
              </div>
              <div>
                <a @click="openUserAssignment(lessonPage)">Submit ulang link</a>
              </div>
            </div>
            <div
              v-if="
                prakerjaData?.scope &&
                prakerjaData?.is_completed == 'true' &&
                prakerjaData?.notes
              "
              class="mt-4"
            >
              <span
                style="
                  font-family: 'Sora', sans-serif;
                  font-size: 16px;
                  font-weight: 700;
                "
              >
                Feedback:</span
              >
              <div>{{ prakerjaData?.notes }}</div>
            </div>
          </template>
          <div v-if="lessonPage.content_type == 'multiple_choice'">
            <div class="choice-area">
              <a
                @click="cekChoice(i)"
                class="btn-choice"
                :class="{
                  true: i.checked == 'true',
                  false: i.checked == 'false',
                }"
                v-for="(i, idx) in lessonChoices"
                :key="idx"
                >{{ i.answer }}</a
              >
              <div v-if="answerCanBeMoreThanOne">
                <p>* Jawaban boleh lebih dari 1</p>
              </div>
            </div>
          </div>
          <!-- Answer -->
          <div v-if="lessonPage.content_type == 'answer'">
            <div class="answer-area f-form">
              <div class="f-label">Your answer</div>
              <v-textarea
                outlined
                dense
                placeholder="Tulis disini"
                background-color="#fff"
                v-model="answer"
                :class="{ true: alertTrue, false: alertFalse }"
              ></v-textarea>
              <div class="cta" v-if="btnAnswer">
                <a @click="cekAnswer()" class="btn btn-blue btn-block"
                  >Submit</a
                >
              </div>
              <v-row class="btn-answer" v-if="isAnswer === false">
                <v-col cols="6">
                  <a class="btn-tryagain" @click="tryAnswer()">Try again!</a>
                </v-col>
                <v-col cols="6">
                  <a class="btn-giveup" @click="giveupAnswer()">Give up!</a>
                </v-col>
              </v-row>
            </div>
          </div>
        </div>
        <div
          class="cta-area"
          v-if="
            lessonPage.content_type != 'answer' &&
            lessonPage.content_type != 'fill_blank' &&
            lessonPage.content_type != 'multiple_choice'
          "
        >
          <!-- Scope TPM / UK -->
          <a
            v-if="prakerjaData?.scope && !prakerjaData?.answer"
            @click="openUserAssignment(lessonPage)"
            class="btn btn-blue btn-block"
            >Submit Link</a
          >
          <a v-else @click="nextLesson()" class="btn btn-blue btn-block"
            >Lanjut</a
          >
        </div>
        <v-scroll-y-reverse-transition>
          <div class="popup-answer-false" v-if="alertFalse">
            <div class="head">
              <div class="heading">
                <img src="@/assets/img/emot-false.png" alt="" />
                <span>Incorrect!</span>
              </div>
              <a @click="alertFalse = false" class="close">
                <img src="@/assets/img/icn-close.png" alt="" />
              </a>
            </div>
            <p>
              {{
                alertText != "" && alertText
                  ? alertText
                  : "Jawaban kamu salah, coba lagi ya!"
              }}
            </p>
            <div class="cta" v-if="haveBeenCorrect">
              <a @click="nextLesson()" class="btn btn-blue btn-block">Lanjut</a>
            </div>
          </div>
        </v-scroll-y-reverse-transition>
        <v-scroll-y-reverse-transition>
          <div class="popup-answer-true" v-if="alertTrue">
            <div class="head">
              <div class="heading">
                <img src="@/assets/img/emot-true.png" alt="" />
                <span>Correct!</span>
              </div>
              <a @click="alertTrue = false" class="close">
                <img src="@/assets/img/icn-close.png" alt="" />
              </a>
            </div>
            <p>
              {{
                alertText != "" && alertText ? alertText : "Jawaban Kamu Benar!"
              }}
            </p>
            <div class="cta" v-if="haveBeenCorrect">
              <a @click="nextLesson()" class="btn btn-blue btn-block">Lanjut</a>
            </div>
          </div>
        </v-scroll-y-reverse-transition>
      </div>
      <v-dialog
        v-model="dialogSubmit"
        transition="dialog-bottom-transition"
        max-width="600"
      >
        <template v-slot:default="dialogSubmit">
          <v-card>
            <v-toolbar color="#09408e" dark>Submit Link File</v-toolbar>
            <v-card-text>
              <v-form class="mt-24">
                <v-textarea
                  v-model="url_file"
                  label="Url File"
                  placeholder="Masukkan Link File"
                  required
                ></v-textarea>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="submitUserAssignment()"
                  :disabled="addLoading || !url_file"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Submit" }}
                </button>
                <a
                  @click="dialogSubmit.value = false"
                  class="btn btn-text btn-sm tf"
                  >Cancel</a
                >
              </div>
            </v-card-text>
          </v-card>
        </template>
      </v-dialog>
    </div>
    <div v-else>
      <Header />
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <v-row>
        <!-- Main Content for Desktop -->
        <v-col cols="12" class="main-content">
          <NavbarFullDesktop :title="`Lesson`" />
          <div class="content-container">
            <div class="header-area">
              <a @click="backLesson()">
                <div class="icon">
                  <img src="@/assets/img/bar-back.png" alt="" />
                </div>
              </a>
              <div class="progress-area">
                <v-progress-linear
                  :value="((lessonIdx + 1) / lessons.length) * 100"
                  color="#0056d2"
                  background-color="#D8E3E7"
                  rounded
                ></v-progress-linear>
              </div>
              <a @click="$router.go(-1)">
                <div class="icon">
                  <v-icon color="#0056D2">mdi-close-circle</v-icon>
                </div>
              </a>
            </div>
            <div class="content-area">
              <div class="titles-desktop">{{ lessonPage.name }}</div>
              <!-- Content Image -->
              <div v-if="lessonPage.media_type == 'image'">
                <div class="media">
                  <ZoomableImage
                    :src="`${storageBaseUrl}${lessonPage.media_link}`"
                    width="100%"
                    height="100%"
                  />
                </div>
              </div>
              <!-- Content Pdf -->
              <div v-else-if="lessonPage.media_type == 'pdf'">
                <PdfViewer
                  :source="`${storageBaseUrl}${lessonPage.media_link}`"
                ></PdfViewer>
              </div>
              <!-- Content Video -->
              <div v-if="lessonPage.media_type == 'video'">
                <div class="media">
                  <div class="iframe">
                    <iframe
                      :src="
                        `https://www.youtube.com/embed/` + lessonPage.media_link
                      "
                      frameborder="0"
                      allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
                  </div>
                </div>
              </div>

              <div v-if="lessonPage.content_type == 'fill_blank'">
                <!-- <div class="content" v-html="lessonData.content"></div> -->
                <ContentSection :content="lessonData.content" />
                <div class="choice-area">
                  <a
                    @click="cekFillBlank(i)"
                    class="btn-choice"
                    :class="{
                      true: i.checked == 'true',
                      false: i.checked == 'false',
                    }"
                    v-for="(i, idx) in lessonFillblankChoices"
                    :key="idx"
                    >{{ i.value }}</a
                  >
                </div>
              </div>
              <!-- <div class="content" v-else v-html="lessonData.content"></div> -->
              <ContentSection v-else :content="lessonData.content" />
              <div
                v-if="
                  prakerjaData?.scope &&
                  prakerjaData?.answer &&
                  prakerjaData?.is_completed == 'true'
                "
                class="mt-4"
              >
                <span
                  style="
                    font-family: 'Sora', sans-serif;
                    font-size: 16px;
                    font-weight: 700;
                  "
                  >Link Submission:</span
                >
                <div>
                  <a :href="prakerjaData?.answer" target="_blank">Buka link</a>
                </div>
                <div>
                  <a @click="openUserAssignment(lessonPage)"
                    >Submit ulang link</a
                  >
                </div>
              </div>
              <div
                v-if="
                  prakerjaData?.scope &&
                  prakerjaData?.is_completed == 'true' &&
                  prakerjaData?.notes
                "
                class="mt-4"
              >
                <span
                  style="
                    font-family: 'Sora', sans-serif;
                    font-size: 16px;
                    font-weight: 700;
                  "
                >
                  Feedback:</span
                >
                <div>{{ prakerjaData?.notes }}</div>
              </div>
              <div v-if="lessonPage.content_type == 'multiple_choice'">
                <div class="choice-area">
                  <a
                    @click="cekChoice(i)"
                    class="btn-choice"
                    :class="{
                      true: i.checked == 'true',
                      false: i.checked == 'false',
                    }"
                    v-for="(i, idx) in lessonChoices"
                    :key="idx"
                    >{{ i.answer }}</a
                  >
                  <div v-if="answerCanBeMoreThanOne">
                    <p>* Jawaban boleh lebih dari 1</p>
                  </div>
                </div>
              </div>
              <!-- Answer -->
              <div v-if="lessonPage.content_type == 'answer'">
                <div class="answer-area f-form">
                  <div class="f-label">Your answer</div>
                  <v-textarea
                    outlined
                    dense
                    placeholder="Tulis disini"
                    background-color="#fff"
                    v-model="answer"
                    :class="{ true: alertTrue, false: alertFalse }"
                  ></v-textarea>
                  <div class="cta" v-if="btnAnswer">
                    <a @click="cekAnswer()" class="btn btn-blue btn-block"
                      >Submit</a
                    >
                  </div>
                  <v-row class="btn-answer" v-if="isAnswer === false">
                    <v-col cols="6">
                      <a class="btn-tryagain" @click="tryAnswer()"
                        >Try again!</a
                      >
                    </v-col>
                    <v-col cols="6">
                      <a class="btn-giveup" @click="giveupAnswer()">Give up!</a>
                    </v-col>
                  </v-row>
                </div>
              </div>
            </div>
            <div
              class="cta-area-desktop"
              v-if="
                lessonPage.content_type != 'answer' &&
                lessonPage.content_type != 'fill_blank' &&
                lessonPage.content_type != 'multiple_choice'
              "
            >
              <!-- Scope TPM / UK -->
              <a
                v-if="prakerjaData?.scope && !prakerjaData?.answer"
                @click="openUserAssignment(lessonPage)"
                class="btn btn-blue btn-block"
                >Submit Link</a
              >
              <a v-else @click="nextLesson()" class="btn btn-blue btn-block"
                >Lanjut</a
              >
            </div>
            <v-scroll-y-reverse-transition>
              <div class="popup-answer-false" v-if="alertFalse">
                <div class="head">
                  <div class="heading">
                    <img src="@/assets/img/emot-false.png" alt="" />
                    <span>Salah!</span>
                  </div>
                  <a @click="alertFalse = false" class="close">
                    <img src="@/assets/img/icn-close.png" alt="" />
                  </a>
                </div>
                <p>
                  {{
                    alertText != "" && alertText
                      ? alertText
                      : "Jawaban kamu salah, coba lagi ya!"
                  }}
                </p>
                <div class="cta" v-if="haveBeenCorrect">
                  <a @click="nextLesson()" class="btn btn-blue btn-block"
                    >Lanjut</a
                  >
                </div>
              </div>
            </v-scroll-y-reverse-transition>
            <v-scroll-y-reverse-transition>
              <div class="popup-answer-true" v-if="alertTrue">
                <div class="head">
                  <div class="heading">
                    <img src="@/assets/img/emot-true.png" alt="" />
                    <span>Benar!</span>
                  </div>
                  <a @click="alertTrue = false" class="close">
                    <img src="@/assets/img/icn-close.png" alt="" />
                  </a>
                </div>
                <p>
                  {{
                    alertText != "" && alertText
                      ? alertText
                      : "Jawaban Kamu Benar!"
                  }}
                </p>
                <div class="cta" v-if="haveBeenCorrect">
                  <a @click="nextLesson()" class="btn btn-blue btn-block"
                    >Lanjut</a
                  >
                </div>
              </div>
            </v-scroll-y-reverse-transition>
          </div>
        </v-col>
      </v-row>
      <v-dialog
        v-model="dialogSubmit"
        transition="dialog-bottom-transition"
        max-width="600"
      >
        <template v-slot:default="dialogSubmit">
          <v-card>
            <v-toolbar color="#09408e" dark>Submit Link File</v-toolbar>
            <v-card-text>
              <v-form class="mt-24">
                <v-textarea
                  v-model="url_file"
                  label="Url File"
                  placeholder="Masukkan Link File"
                  required
                ></v-textarea>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="submitUserAssignment()"
                  :disabled="addLoading || !url_file"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Submit" }}
                </button>
                <a
                  @click="dialogSubmit.value = false"
                  class="btn btn-text btn-sm tf"
                  >Cancel</a
                >
              </div>
            </v-card-text>
          </v-card>
        </template>
      </v-dialog>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import ContentSection from "@/components/ContentSection.vue";
import ZoomableImage from "@/components/ZoomableImage.vue";
import PdfViewer from "@/components/PdfViewer.vue";
import { isMobile } from "@/helpers/deviceDetect";
import NavbarFullDesktop from "@/components/NavbarFullDesktop.vue";

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
    Loading,
    Alert,
    Header,
    ContentSection,
    ZoomableImage,
    PdfViewer,
    NavbarFullDesktop,
  },
  data() {
    return {
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      isMobile: false,
      dialogSubmit: false,
      addLoading: false,
      url_file: "",
      // localStorage
      prakerjaData: null,
      userId: "",
      savedAnswerParsed: {},
      lastIndexParsed: {},

      course_id: "",
      lessonDetail: {},
      id: "",
      lessonPage: {},
      lessons: [],
      lessonIdx: 0,
      banner: null,
      lessonData: {},
      lessonTooltips: [],
      lessonFillblank: [],
      lessonFillblankChoices: [],
      lessonChoices: [],
      lessonAnswer: "",
      course: {},
      courseCategory: {},
      isPrakerja: false,
      answer: "",
      answerCanBeMoreThanOne: false,

      btnAnswer: true,
      isAnswer: "",
      alertTrue: false,
      alertFalse: false,
      alertText: null,
      haveBeenCorrect: false,

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: true,

      type: "",
    };
  },
  watch: {
    alertTrue: function (newAlert) {
      if (newAlert == true) {
        this.haveBeenCorrect = true;
      }
    },
  },
  methods: {
    cekChoice(item) {
      let itemChoicesIdx = this.lessonChoices.findIndex(
        (e) => e.answer === item.answer
      );
      this.lessonChoices = this.lessonChoices.map((e) => {
        if (e.answer == item.answer) {
          e.picked = true;
          return e;
        } else {
          e.picked = false;
          return e;
        }
      });

      if (item.checked == true || item.checked == "true") {
        this.alertFalse = false;
        this.alertText = item.textPopup;
        item.checked = "true";
        this.lessonChoices[itemChoicesIdx].checked = "true";
        this.savedAnswerParsed[this.lessonPage.id] = {
          lessonChoices: this.lessonChoices,
        };
        window.localStorage.setItem(
          `savedAnswer-${this.userId}`,
          JSON.stringify(this.savedAnswerParsed)
        );
      } else {
        this.alertTrue = false;
        this.alertFalse = true;
        this.alertText = item.textPopup;
        item.checked = "false";
        setTimeout(() => {
          item.checked = false;
          this.lessonChoices[itemChoicesIdx].checked = false;
        }, 1000);
      }

      const unpickedCorrectAnswer = this.lessonChoices.filter((e) => {
        return e.checked === true;
      });
      if (unpickedCorrectAnswer.length == 0 && this.alertFalse === false) {
        this.alertTrue = true;
      }
    },
    cekFillBlank(item) {
      // Do nothing if already checked
      if (item.checked == "true" || item.checked == true) {
        return;
      }

      let tmpFillBlank = this.lessonFillblank.filter(
        (e) => e.checked != "true"
      );
      let itemFillBlankIdx = this.lessonFillblank.findIndex(
        (e) => e.key === item.key
      );
      if (tmpFillBlank.length > 0) {
        if (tmpFillBlank[0].key == item.key) {
          item.checked = "true";
          this.lessonFillblank[itemFillBlankIdx].checked = "true";
          this.alertFalse = false;
          this.lessonData.content = this.lessonData.content.replace(
            `<span class="fill false">${item.value}</span>`,
            `<span class="fill true">${item.value}</span>`
          );
          this.lessonData.content = this.lessonData.content.replace(
            `<span class="fill none">${item.value}</span>`,
            `<span class="fill true">${item.value}</span>`
          );
          this.savedAnswerParsed[this.lessonPage.id] = {
            lessonFillblank: this.lessonFillblank,
            lessonFillblankChoices: this.lessonFillblankChoices,
          };
          window.localStorage.setItem(
            `savedAnswer-${this.userId}`,
            JSON.stringify(this.savedAnswerParsed)
          );
        } else {
          this.alertTrue = false;
          this.alertFalse = true;
          item.checked = "false";
          this.lessonFillblank[itemFillBlankIdx].checked = "false";
          this.lessonData.content = this.lessonData.content.replace(
            `<span class="fill none">${tmpFillBlank[0].value}</span>`,
            `<span class="fill false">${tmpFillBlank[0].value}</span>`
          );
          setTimeout(() => {
            item.checked = "none";
            this.lessonFillblank[itemFillBlankIdx].checked = "none";
          }, 1000);
        }

        let allCheckTrue = true;
        this.lessonFillblank.forEach((e) => {
          if (!(e.checked == "true" || e.checked == true)) {
            allCheckTrue = false;
          }
        });

        if (allCheckTrue) {
          this.alertTrue = true;
        }
      }
      setTimeout(() => {
        this.alertFalse = false;
      }, 2000);
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
    cekAnswer() {
      this.btnAnswer = false;
      var keyword = this.lessonData.keyword.split(",");
      let isTrue = true;
      for (var y = 0; y < keyword.length; y++) {
        if (!this.answer.includes(keyword[y].trim())) {
          isTrue = false;
        }
      }
      if (isTrue) {
        this.isAnswer = true;
        this.alertTrue = true;
        this.savedAnswerParsed[this.lessonPage.id] = {
          answer: this.answer,
          alertTrue: true,
        };
        window.localStorage.setItem(
          `savedAnswer-${this.userId}`,
          JSON.stringify(this.savedAnswerParsed)
        );
      } else {
        this.isAnswer = false;
        this.alertFalse = true;
      }
    },
    tryAnswer() {
      this.btnAnswer = true;
      this.answer = "";
      this.isAnswer = "";
      this.alertFalse = false;
    },
    giveupAnswer() {
      this.isAnswer = true;
      this.alertFalse = false;
      this.alertTrue = true;
      this.answer = this.lessonData.keyword.replace(/,/g, "");
      this.savedAnswerParsed[this.lessonPage.id] = {
        answer: this.answer,
        alertTrue: true,
      };
      window.localStorage.setItem(
        `savedAnswer-${this.userId}`,
        JSON.stringify(this.savedAnswerParsed)
      );
    },
    getLesson(id) {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/lesson/read/${id}`,
        })
        .then((res) => {
          // console.log("l", res);
          if (res.data.success) {
            this.isReady = true;
            this.lessonDetail = res.data.data.Lesson;
            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.loadScreen = false;
        });
    },
    getLessonPages(id) {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/lesson-page?limit=999&sortBy=sorter&orderBy=asc&lessons_id=${id}`,
        })
        .then((res) => {
          // console.log("lp", res);
          if (res.data.success) {
            this.isReady = true;
            this.lessons = res.data.data.LessonPages.rows;
            this.lessonPage = this.lessons[this.lessonIdx];
            this.lessonData = JSON.parse(this.lessonPage.data);
            this.lessonTooltips = JSON.parse(this.lessonPage.tooltips);
            this.haveBeenCorrect = false;

            if (this.lessonTooltips.length > 0) {
              for (var i = 0; i < this.lessonTooltips.length; i++) {
                this.lessonData.content = this.lessonData.content.replace(
                  this.lessonTooltips[i].key,
                  `<span class="hover-text">${this.lessonTooltips[i].value}
                    <span class="tooltip-text" id="bottom">${this.lessonTooltips[i].message}</span>
                  </span>`
                );
                // console.log(this.lessonData.content);
              }
            }

            // Fillblank
            if (this.lessonData.answer) {
              this.lessonFillblank = this.lessonData.answer.slice();
              this.lessonFillblankChoices = this.shuffle(
                this.lessonData.answer.slice()
              );
              if (this.savedAnswerParsed[this.lessonPage.id]) {
                this.lessonFillblank =
                  this.savedAnswerParsed[this.lessonPage.id].lessonFillblank;
                this.lessonFillblankChoices =
                  this.savedAnswerParsed[
                    this.lessonPage.id
                  ].lessonFillblankChoices;
              }
              if (this.lessonFillblank.length > 0) {
                let allCheckTrue = true;
                for (var j = 0; j < this.lessonFillblank.length; j++) {
                  this.lessonFillblank[j].checked = this.lessonFillblank[j]
                    .checked
                    ? this.lessonFillblank[j].checked
                    : "none";
                  this.lessonFillblankChoices[j].checked = this
                    .lessonFillblankChoices[j].checked
                    ? this.lessonFillblankChoices[j].checked
                    : "none";
                  this.lessonData.content = this.lessonData.content.replace(
                    this.lessonFillblank[j].key,
                    `<span class="fill ${this.lessonFillblank[j].checked}">${this.lessonFillblank[j].value}</span>`
                  );

                  if (
                    !(
                      this.lessonFillblank[j].checked == "true" ||
                      this.lessonFillblank[j].checked == true
                    )
                  ) {
                    allCheckTrue = false;
                  }
                }

                if (allCheckTrue) {
                  this.alertTrue = true;
                }
              }
            }
            if (this.lessonData.choices) {
              this.lessonChoices = this.lessonData.choices;
              const correntAnswer = this.lessonChoices.filter((e) => {
                return e.checked === true;
              });
              if (correntAnswer.length > 1) {
                this.answerCanBeMoreThanOne = true;
              }

              if (this.savedAnswerParsed[this.lessonPage.id]) {
                this.lessonChoices =
                  this.savedAnswerParsed[this.lessonPage.id].lessonChoices;
                const picked = this.lessonChoices.find((e) => e.picked == true);
                if (picked) {
                  this.alertText = picked.textPopup;
                }

                this.lessonChoices = this.lessonChoices.map((e) => {
                  if (e.checked == "false") {
                    e.checked = false;
                  }
                  return e;
                });

                const unpickedCorrectAnswer = this.lessonChoices.filter((e) => {
                  return e.checked === true;
                });
                if (
                  unpickedCorrectAnswer.length == 0 &&
                  this.alertFalse === false
                ) {
                  this.alertTrue = true;
                }
              }
            }
            if (this.lessonData.keyword) {
              this.lessonAnswer = this.lessonData.keyword;
              if (this.savedAnswerParsed[this.lessonPage.id]) {
                this.answer = this.savedAnswerParsed[this.lessonPage.id].answer;
                this.alertTrue =
                  this.savedAnswerParsed[this.lessonPage.id].alertTrue;
              }
            }

            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
            // setTimeout(() => {
            //   this.$router.push({
            //     path: `/home`,
            //   });
            // }, 2000);
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getCourseData(id) {
      return this.$root
        .axios({
          method: "get",
          url: `/v1/course/read/${id}`,
        })
        .then((res) => {
          // console.log("🚀 ~ file: LessonPage.vue:475 ~ .then ~ res:", res);
          const course = res.data.data.Course;
          this.course = course;
          this.courseCategory = course.course_category;
          this.isPrakerja = course.course_category.type === "prakerja";
        });
    },
    fetchSubmitUserAssignment() {
      this.addLoading = true;
      const data = {
        redeem_code: this.prakerjaData?.redeem_code,
        scope: this.prakerjaData?.scope,
        sequence: this.prakerjaData?.sequence,
        url_file: this.url_file,
        courses_id: this.prakerjaData?.course_id,
        lessons_id: this.prakerjaData?.lesson_id,
        lesson_pages_id: this.lessonPage.id,
      };
      // console.log("Prakerja Data:", this.prakerjaData);
      this.$root
        .axios({
          method: "post",
          url: `/v1/user-lesson/answer`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.fetchUserLessonCreate();
            this.alert("Berhasil mensubmit jawaban", "success");
            this.dialogSubmit = false;
            this.url_file = "";
            // Remove localStorage prakerjaData
            localStorage.removeItem("prakerjaData");
          } else {
            this.alert(res.data.message, "danger");
          }
          this.addLoading = false;
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },
    fetchUserLessonCreate() {
      if (this.lessonDetail.is_completed == "false") {
        var data = {
          courses_id: this.course_id,
          lessons_id: this.id,
        };
        this.$root
          .axios({
            method: "post",
            url: `/v1/user-lesson/create`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              if (this.isPrakerja) {
                this.$router.push({
                  path: `/topic/${this.course_id}`,
                });
              } else {
                this.$router.push({
                  path: `/topic/${this.course_id}/lesson/${this.id}/rating`,
                });
              }
            } else {
              if (res.data.message === "Duplicate entry lesson id!") {
                this.$router.push({
                  path: `/topic/${this.course_id}/lesson/${this.id}/rating`,
                });
              }
            }
          });
      } else {
        if (this.isPrakerja) {
          this.$router.push({
            path: `/topic/${this.course_id}`,
          });
        } else if (this.lessonDetail.is_feedbacked == "false") {
          this.$router.push({
            path: `/topic/${this.course_id}/lesson/${this.id}/rating`,
          });
        } else {
          this.$router.go(-1);
        }
      }
    },
    submitUserAssignment() {
      this.fetchSubmitUserAssignment();
    },
    openUserAssignment(item) {
      console.log(item);
      this.dialogSubmit = true;
    },
    nextLesson() {
      this.isLoading = true;
      this.alertTrue = false;
      this.alertFalse = false;
      setTimeout(() => {
        this.lessonIdx += 1;
        if (this.lessonIdx < this.lessons.length) {
          this.lastIndexParsed[this.id] = this.lessonIdx;
          window.localStorage.setItem(
            `lastLessonPageIndex-${this.userId}`,
            JSON.stringify(this.lastIndexParsed)
          );
          this.lessonPage = this.lessons[this.lessonIdx];
          this.lessonData = JSON.parse(this.lessonPage.data);
          this.lessonTooltips = JSON.parse(this.lessonPage.tooltips);
          this.haveBeenCorrect = false;
          if (this.lessonTooltips.length > 0) {
            for (var i = 0; i < this.lessonTooltips.length; i++) {
              this.lessonData.content = this.lessonData.content.replace(
                this.lessonTooltips[i].key,
                `<span class="hover-text">${this.lessonTooltips[i].value}
                    <span class="tooltip-text" id="bottom">${this.lessonTooltips[i].message}</span>
                  </span>`
              );
            }
          }

          if (this.lessonData.answer) {
            this.lessonFillblank = this.lessonData.answer.slice();
            this.lessonFillblankChoices = this.shuffle(
              this.lessonData.answer.slice()
            );
            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.lessonFillblank =
                this.savedAnswerParsed[this.lessonPage.id].lessonFillblank;
              this.lessonFillblankChoices =
                this.savedAnswerParsed[
                  this.lessonPage.id
                ].lessonFillblankChoices;
            }
            if (this.lessonFillblank.length > 0) {
              let allCheckTrue = true;
              for (var j = 0; j < this.lessonFillblank.length; j++) {
                this.lessonFillblank[j].checked = this.lessonFillblank[j]
                  .checked
                  ? this.lessonFillblank[j].checked
                  : "none";
                this.lessonFillblankChoices[j].checked = this
                  .lessonFillblankChoices[j].checked
                  ? this.lessonFillblankChoices[j].checked
                  : "none";
                this.lessonData.content = this.lessonData.content.replace(
                  this.lessonFillblank[j].key,
                  `<span class="fill ${this.lessonFillblank[j].checked}">${this.lessonFillblank[j].value}</span>`
                );

                if (
                  !(
                    this.lessonFillblank[j].checked == "true" ||
                    this.lessonFillblank[j].checked == true
                  )
                ) {
                  allCheckTrue = false;
                }
              }

              if (allCheckTrue) {
                this.alertTrue = true;
              }
            }
          }
          if (this.lessonData.choices) {
            this.lessonChoices = this.lessonData.choices;
            const correntAnswer = this.lessonChoices.filter((e) => {
              return e.checked === true;
            });
            if (correntAnswer.length > 1) {
              this.answerCanBeMoreThanOne = true;
            }

            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.lessonChoices =
                this.savedAnswerParsed[this.lessonPage.id].lessonChoices;
              const picked = this.lessonChoices.find((e) => e.picked == true);
              if (picked) {
                this.alertText = picked.textPopup;
              }

              this.lessonChoices = this.lessonChoices.map((e) => {
                if (e.checked == "false") {
                  e.checked = false;
                }
                return e;
              });

              const unpickedCorrectAnswer = this.lessonChoices.filter((e) => {
                return e.checked === true;
              });
              if (
                unpickedCorrectAnswer.length == 0 &&
                this.alertFalse === false
              ) {
                this.alertTrue = true;
              }
            }
          }
          if (this.lessonData.keyword) {
            this.lessonAnswer = this.lessonData.keyword;
            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.answer = this.savedAnswerParsed[this.lessonPage.id].answer;
              this.alertTrue =
                this.savedAnswerParsed[this.lessonPage.id].alertTrue;
            }
          }
          // console.log(this.lessonPage);
        } else {
          this.clearSavedAnswer();
          this.fetchUserLessonCreate();
        }
        this.isLoading = false;
      }, 500);
    },
    backLesson() {
      this.isLoading = true;
      this.alertTrue = false;
      this.alertFalse = false;
      setTimeout(() => {
        this.lessonIdx -= 1;
        if (this.lessonIdx >= 0) {
          this.lastIndexParsed[this.id] = this.lessonIdx;
          window.localStorage.setItem(
            `lastLessonPageIndex-${this.userId}`,
            JSON.stringify(this.lastIndexParsed)
          );
          this.lessonPage = this.lessons[this.lessonIdx];
          this.lessonData = JSON.parse(this.lessonPage.data);
          this.lessonTooltips = JSON.parse(this.lessonPage.tooltips);
          this.haveBeenCorrect = false;
          if (this.lessonTooltips.length > 0) {
            for (var i = 0; i < this.lessonTooltips.length; i++) {
              this.lessonData.content = this.lessonData.content.replace(
                this.lessonTooltips[i].key,
                `<span class="hover-text">${this.lessonTooltips[i].value}
                  <span class="tooltip-text" id="bottom">${this.lessonTooltips[i].message}</span>
                  </span>`
              );
            }
          }

          if (this.lessonData.answer) {
            this.lessonFillblank = this.lessonData.answer.slice();
            this.lessonFillblankChoices = this.shuffle(
              this.lessonData.answer.slice()
            );
            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.lessonFillblank =
                this.savedAnswerParsed[this.lessonPage.id].lessonFillblank;
              this.lessonFillblankChoices =
                this.savedAnswerParsed[
                  this.lessonPage.id
                ].lessonFillblankChoices;
            }
            if (this.lessonFillblank.length > 0) {
              let allCheckTrue = true;
              for (var j = 0; j < this.lessonFillblank.length; j++) {
                this.lessonFillblank[j].checked = this.lessonFillblank[j]
                  .checked
                  ? this.lessonFillblank[j].checked
                  : "none";
                this.lessonFillblankChoices[j].checked = this
                  .lessonFillblankChoices[j].checked
                  ? this.lessonFillblankChoices[j].checked
                  : "none";
                this.lessonData.content = this.lessonData.content.replace(
                  this.lessonFillblank[j].key,
                  `<span class="fill ${this.lessonFillblank[j].checked}">${this.lessonFillblank[j].value}</span>`
                );

                if (
                  !(
                    this.lessonFillblank[j].checked == "true" ||
                    this.lessonFillblank[j].checked == true
                  )
                ) {
                  allCheckTrue = false;
                }
              }

              if (allCheckTrue) {
                this.alertTrue = true;
              }
            }
          }
          if (this.lessonData.choices) {
            this.lessonChoices = this.lessonData.choices;
            const correntAnswer = this.lessonChoices.filter((e) => {
              return e.checked === true;
            });
            if (correntAnswer.length > 1) {
              this.answerCanBeMoreThanOne = true;
            }

            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.lessonChoices =
                this.savedAnswerParsed[this.lessonPage.id].lessonChoices;
              const picked = this.lessonChoices.find((e) => e.picked == true);
              if (picked) {
                this.alertText = picked.textPopup;
              }

              this.lessonChoices = this.lessonChoices.map((e) => {
                if (e.checked == "false") {
                  e.checked = false;
                }
                return e;
              });

              const unpickedCorrectAnswer = this.lessonChoices.filter((e) => {
                return e.checked === true;
              });
              if (
                unpickedCorrectAnswer.length == 0 &&
                this.alertFalse === false
              ) {
                this.alertTrue = true;
              }
            }
          }
          if (this.lessonData.keyword) {
            this.lessonAnswer = this.lessonData.keyword;
            if (this.savedAnswerParsed[this.lessonPage.id]) {
              this.answer = this.savedAnswerParsed[this.lessonPage.id].answer;
              this.alertTrue =
                this.savedAnswerParsed[this.lessonPage.id].alertTrue;
            }
          }
          // console.log(this.lessonPage);
        } else {
          this.$router.go(-1);
        }
        this.isLoading = false;
      }, 500);
    },
    clearSavedAnswer() {
      for (let i = 0; i < this.lessons.length; i++) {
        const lessonPage = this.lessons[i];
        delete this.savedAnswerParsed[lessonPage.id];
      }
      window.localStorage.setItem(
        `savedAnswer-${this.userId}`,
        JSON.stringify(this.savedAnswerParsed)
      );
      delete this.lastIndexParsed[this.id];
      window.localStorage.setItem(
        `lastLessonPageIndex-${this.userId}`,
        JSON.stringify(this.lastIndexParsed)
      );
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
  },
  mounted() {
    if (this.$root.token()) {
      this.isMobile = isMobile();
      var id = this.$route.params.id;
      this.course_id = this.$route.params.id_topic;
      this.getCourseData(this.course_id);
      var type = this.$route.query.type;
      if (id) {
        this.prakerjaData = JSON.parse(localStorage.getItem("prakerjaData"));
        this.id = id;
        this.userId = this.$root.userId();

        const lastIndex = window.localStorage.getItem(
          `lastLessonPageIndex-${this.userId}`
        );
        this.lastIndexParsed = lastIndex ? JSON.parse(lastIndex) : {};
        this.lessonIdx = this.lastIndexParsed[this.id] || 0;
        this.lessonIdx = parseInt(this.lessonIdx);

        const savedAnswer = window.localStorage.getItem(
          `savedAnswer-${this.userId}`
        );
        this.savedAnswerParsed = savedAnswer ? JSON.parse(savedAnswer) : {};
      }
      if (type) {
        this.type = type;
      }
      this.getLessonPages(id);
      this.getLesson(id);
    }
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
    // vertical-align: middle;
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
    // vertical-align: middle;
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

.main-content {
  max-width: 100%;
  padding: 0;
  gap: 24px;
  height: 100%;
  position: relative;
  background: white !important;
  min-height: 100vh !important;

  .content-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 26px 244px 26px 244px;

    .header-area {
      display: flex;
      align-items: center;
      padding: 0px 0 20px;
      width: 80%;
      justify-content: center;

      .icon {
        line-height: 0;

        img {
          height: 18px;
        }
      }

      .progress-area {
        width: 80%;
        padding: 0 16px;
      }
    }

    .header {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      color: #1e1e1e;
      font-family: Sora;
      font-size: 40px;
      font-style: normal;
      font-weight: 600;
      line-height: 150%;
      letter-spacing: -0.6px;
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
      padding: 18px 24px;
      position: fixed;
      bottom: 0;
      width: 100%;
      max-width: 414px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9;
    }
  }
}

.content-area {
  padding-bottom: 5rem;
  max-width: 750px;

  .titles {
    font-weight: 700;
    font-size: 16px;
    color: $main-color;
    text-align: center;
    padding: 10px 0 40px;
  }

  .titles-desktop {
    color: $main-color;
    text-align: center;
    padding: 10px 0 40px;
    font-family: Sora;
    font-size: 32px;
    font-style: normal;
    font-weight: 600;
    line-height: 150%;
    text-transform: uppercase;
    letter-spacing: -0.384px;
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
    word-wrap: break-word;

    .tooltip {
      font-weight: 700;
      text-decoration: underline;
      cursor: pointer;
    }

    .fill {
      font-weight: 700;
      cursor: pointer;
      position: relative;
      display: inline-block;
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
  }

  .choice-area {
    margin-top: 40px;

    .btn-choice {
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
    font-size: 14px;
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
    font-size: 14px;
  }
}

.cta-area-desktop {
  padding: 18px 20px;
  position: fixed;
  bottom: 0;
  width: 100%;
  max-width: 414px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 9;
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
    color: #57ea5d;
  }

  .fill.true::before {
    display: none;
  }

  .fill.false::before {
    border: 1px solid #ea5757;
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
  margin-top: -100px;
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
