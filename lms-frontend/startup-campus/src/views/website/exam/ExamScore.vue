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

      <div class="rating-area">
        <div class="imgs">
          <img src="@/assets/img/checkblue.svg" alt="" />
        </div>
        <div class="heading">Selamat</div>
        <div class="desc">Tes kamu sudah selesai!</div>
        <div class="desc" v-if="!aboveKKM">Kamu belum memenuhi nilai KKM</div>
        <div class="progress-wrapper">
          <div class="score">
            <div class="kkm" v-if="exam?.kkm != null">KKM: {{ exam.kkm }}</div>
            <div class="txt">Skor Kamu</div>
            <div class="value">{{ nilai }}</div>
          </div>
          <div class="v-one">0</div>
          <div class="v-two">100</div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            x="0px"
            y="0px"
            viewBox="37 -5 120 100"
            width="200"
            height="200"
          >
            <path
              class="background-bar"
              d="M55,90
               A55,55 0 1,1 140,90"
              style="fill: none"
            />
            <path
              class="kkm-bar"
              d="M55,90
               A55,55 0 1,1 140,90"
              style="fill: none"
              :style="{ strokeDashoffset: kkmProgress }"
            />
            <path
              class="score-bar"
              d="M55,90
               A55,55 0 1,1 140,90"
              style="fill: none"
              :style="{
                strokeDashoffset: progress,
              }"
            />
          </svg>
        </div>

        <div class="cta">
          <!-- <router-link
            :to="`/certificate/${cert}`"
            class="btn btn-blue btn-block"
            >Lihat Sertifikat</router-link
          > -->
          <router-link
            :to="`/topic/${courseId}`"
            class="btn btn-white-ot btn-block"
            >Kembali ke Course</router-link
          >
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
            :title="exam ? `Skor ${this.exam.name}` : 'Skor'"
          />
          <div class="content-container">
            <div class="rating-area">
              <div class="imgs">
                <img src="@/assets/img/checkblue.svg" alt="" />
              </div>
              <div class="heading">Selamat</div>
              <div class="desc">Tes kamu sudah selesai!</div>
              <div class="desc" v-if="!aboveKKM">
                Kamu belum memenuhi nilai KKM
              </div>
              <div class="progress-wrapper">
                <div class="score">
                  <div class="kkm" v-if="exam?.kkm != null">
                    KKM: {{ exam.kkm }}
                  </div>
                  <div class="txt">Skor Kamu</div>
                  <div class="value">{{ nilai }}</div>
                </div>
                <div class="v-one-desktop">0</div>
                <div class="v-two-desktop">100</div>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                  x="0px"
                  y="0px"
                  viewBox="37 -5 120 100"
                  width="200"
                  height="200"
                >
                  <path
                    class="background-bar"
                    d="M55,90
               A55,55 0 1,1 140,90"
                    style="fill: none"
                  />
                  <path
                    class="kkm-bar"
                    d="M55,90
               A55,55 0 1,1 140,90"
                    style="fill: none"
                    :style="{ strokeDashoffset: kkmProgress }"
                  />
                  <path
                    class="score-bar"
                    d="M55,90
               A55,55 0 1,1 140,90"
                    style="fill: none"
                    :style="{
                      strokeDashoffset: progress,
                    }"
                  />
                </svg>
              </div>
            </div>
            <div class="cta-area">
              <router-link
                :to="`/topic/${courseId}`"
                class="btn btn-white-ot btn-block"
                >Kembali ke Course</router-link
              >
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import { isMobile } from "@/helpers/deviceDetect";
import NavbarFullDesktop from "@/components/NavbarFullDesktop.vue";
export default {
  name: "ExamScorePage",
  metaInfo() {
    return {
      title: "Exam Score - AcmeLMS",
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
    NavbarFullDesktop,
  },
  computed: {
    aboveKKM() {
      if (this.exam && this.nilai) {
        return this.nilai >= this.exam.kkm;
      }
      return false;
    },
  },
  data() {
    return {
      id: "",
      data: [],
      nilai: "",
      cert: "",
      progress: "",
      kkmProgress: "",
      courseId: "",
      poinId: "",
      exam: null,
      isMobile: false,

      // notif
      addLoading: false,
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
    };
  },
  methods: {
    getExam(examId) {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam/read/${examId}`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.exam = res.data.data.exam;
            if (!this.poinId) {
              this.nilai = Math.round(this.exam.highest_grade);
              if (this.nilai > 100) {
                this.nilai = 100;
              }
              this.progress = this.getProgressBarValue(this.nilai);
            }
            if (this.exam.kkm != null) {
              this.kkmProgress = this.getProgressBarValue(this.exam.kkm);
            }
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getProgressBarValue(val) {
      if (val == 0 && val < 20) {
        return 247;
      } else if (val >= 10 && val < 20) {
        return 230;
      } else if (val >= 20 && val < 30) {
        return 200;
      } else if (val >= 30 && val < 40) {
        return 170;
      } else if (val >= 40 && val < 50) {
        return 150;
      } else if (val >= 50 && val < 60) {
        return 125;
      } else if (val >= 60 && val < 70) {
        return 100;
      } else if (val >= 70 && val < 80) {
        return 80;
      } else if (val >= 80 && val < 90) {
        return 60;
      } else if (val >= 90 && val < 95) {
        return 20;
      } else if (val >= 95 && val < 100) {
        return 10;
      } else if (val == 100) {
        return 0;
      }
      return 0;
    },
    getScore(id) {
      this.loadScreen = true;
      this.isLoading = true;
      const axiosOption = {
        method: "get",
        url: `/v1/exam-poin/${id}`,
      };
      this.$root
        .axios(axiosOption)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.isReady = true;
            this.data = res.data.data.exam;
            var nilai = parseInt(this.data.poin);
            var num = (nilai / this.data.total_exam) * 100;
            this.nilai = Math.round(Math.round(num * 100) / 100);
            // <!-- 247 = 0, 230 = 10, 200 = 20, 170 = 30, 150 = 40, 125 = 50, 100 = 60, 80 = 70, 60 = 80, 20 = 90, 0 = 100 -->

            // Check if nilai exceeds 100, then set it to 100
            if (this.nilai > 100) {
              this.nilai = 100;
            }

            this.progress = this.getProgressBarValue(this.nilai);

            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
            setTimeout(() => {
              this.$router.push({
                path: `/home`,
              });
            }, 2000);
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.loadScreen = false;
        });
    },
    // readCourse() {
    //   this.$root
    //     .axios({
    //       method: "get",
    //       url: `/v1/course/read/${this.$route.params.id_topic}`,
    //     })
    //     .then((res) => {
    //       // console.log("course", res);
    //       if (res.data.success) {
    //         this.cert = res.data.data.Course.user_certificate[0].id;
    //       }
    //     })
    //     .catch((e) => {
    //       console.log(e);
    //     });
    // },
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
    this.isMobile = isMobile();
    var id = this.$route.params.id;
    this.courseId = this.$route.params.id_topic;
    this.poinId = this.$route.params.id_exam_poin;
    if (this.$root.token()) {
      this.getExam(id);
      if (this.poinId) {
        this.getScore(this.poinId);
      }
      // this.readCourse();
    }
  },
};
</script>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;
  position: relative;
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

.progress-wrapper {
  position: relative;
  padding-top: 23px;

  .v-one {
    position: absolute;
    bottom: 0;
    font-weight: 500;
    font-size: 13px;
    color: rgba(43, 44, 39, 0.5);
    left: 33%;
  }

  .v-one-desktop {
    position: absolute;
    bottom: 0;
    font-weight: 500;
    font-size: 13px;
    color: rgba(43, 44, 39, 0.5);
    left: 45%;
  }

  .v-two {
    position: absolute;
    bottom: 0;
    font-weight: 500;
    font-size: 13px;
    color: rgba(43, 44, 39, 0.5);
    right: 30%;
  }

  .v-two-desktop {
    position: absolute;
    bottom: 0;
    font-weight: 500;
    font-size: 13px;
    color: rgba(43, 44, 39, 0.5);
    right: 44%;
  }

  .score {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    margin-top: 12px;

    .kkm {
      font-weight: 400;
      font-size: 10px;
      text-align: center;
      color: #2b2c27;
    }

    .txt {
      font-weight: 500;
      font-size: 13px;
      text-align: center;
      color: #2b2c27;
    }

    .value {
      font-weight: 700;
      font-size: 36px;
      text-align: center;
      color: #0056d2;
    }
  }
}

path {
  stroke-linecap: round;
  stroke-width: 9;
}

.background-bar {
  stroke: #d8e3e7;
}

.kkm-bar {
  stroke: grey;
  stroke-dasharray: 248;
  stroke-dashoffset: 240;
  /* adjust last number for variance */
}

.score-bar {
  stroke: #fdb72b;
  stroke-dasharray: 248;
  stroke-dashoffset: 240;
  /* adjust last number for variance */
}

.rating-area {
  text-align: center;

  .imgs {
    text-align: center;
    padding-top: 30px;

    img {
      width: 60px;
    }
  }

  .heading {
    padding: 15px 0 8px;
    font-weight: 700;
    font-size: 18px;
    text-align: center;
    color: #0056d2;
  }

  .desc {
    font-weight: 500;
    font-size: 14px;
    text-align: center;
    color: rgba(43, 44, 39, 0.75);
  }

  hr {
    margin: 30px 0 40px;
    opacity: 0.3;
  }

  .cta {
    margin: 0 30px;
    margin-top: 130px;
    margin-bottom: 24px;

    .btn {
      margin-bottom: 12px;
    }
  }
}
</style>

<style lang="scss">
.reason-area {
  .v-btn-toggle--group {
    flex-wrap: wrap;
  }

  .v-btn-toggle--rounded {
    border-radius: 35px;
  }

  .v-btn-toggle--group > .v-btn.v-btn {
    background-color: #e0e0e080 !important;
    border-color: transparent;
    margin: 0px 8px 16px 0;
    min-width: auto;
    text-transform: capitalize;
    font-weight: 600;
    border-radius: 35px;
    padding-left: 18px !important;
    padding-right: 18px !important;
  }

  .v-btn-toggle > .v-btn.v-btn--active {
    background-color: #0056d2 !important;
    color: #fff !important;
  }

  .v-btn--active:hover::before,
  .v-btn--active::before {
    opacity: 0 !important;
  }
}
</style>
