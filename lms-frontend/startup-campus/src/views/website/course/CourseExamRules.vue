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
      <div id="content-area" v-if="isReady">
        <div class="header-title">
          <a @click="$router.go(-1)">
            <div class="icon">
              <img src="@/assets/img/icn-back.png" alt="" />
            </div>
          </a>
          <div class="namepage">
            {{ exams.is_quiz ? "Petunjuk Quiz" : "Petunjuk Tes" }}
          </div>
        </div>
        <div class="desc-area" v-if="exams.description">
          <div class="heading">Deskripsi</div>
          <div class="description" v-html="exams.description"></div>
        </div>
        <!-- <div class="desc-area" v-if="!exams.is_quiz">
          <div class="heading">Ini adalah Kuis dengan Waktu</div>
          <div class="description">
            Kamu yakin siap untuk memulai kuis? Kamu akan diberikan
            <b>{{ toTime(timeExam) }}</b>
            untuk menyelesaikannya. Setelah kamu mencapai batas waktu yang
            ditentukan, kamu akan menggunakan satu kali percobaan setiap
            harinya.
          </div>
        </div>
        <div class="desc-area">
          <div class="heading">Kode Etik</div>
          <div class="description">
            Kami berdedikasi untuk melindungi integritas setiap jawabanmu.
            <br /><br />
            Sebagai bagian dari upaya ini, kami telah membuat kode kehormatan
            yang kami minta untuk diikuti semua orang.
            <br /><br />
            Semua peserta didik harus:
            <ul>
              <li>Mengirimkan hasil mereka sendiri.</li>
              <li>Hindari berbagi jawaban dengan orang lain.</li>
              <li>
                Jika terdapat laporkan dugaan pelanggaran, kami akan
                menindaklanjutinya lebih lanjut.
              </li>
            </ul>
          </div>
        </div> -->
      </div>
      <div class="cta-area">
        <router-link
          v-if="haveUnfinishedExam"
          :to="`/topic/${this.$route.params.id_topic}/exam/${this.$route.params.id}/page`"
          class="btn btn-blue btn-block"
          >Lanjut {{ exams.is_quiz ? "Quiz" : "Tes" }}</router-link
        >
        <p
          v-else-if="
            attempt_left != null &&
            attempt_left != undefined &&
            attempt_left == 0
          "
          :style="{
            textAlign: 'center',
          }"
        >
          Kamu sudah mencapai batas percobaan
        </p>
        <a v-else @click="submitPoin()" class="btn btn-blue btn-block">{{
          isLoading ? "Loading..." : exams.is_quiz ? "Mulai Quiz" : "Mulai Tes"
        }}</a>
      </div>
    </div>
    <div v-else>
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <v-row>
        <!-- Main Content for Desktop -->
        <v-col cols="12" class="main-content">
          <NavbarFullDesktop
            :title="exams.is_quiz ? 'Petunjuk Quiz' : 'Petunjuk Tes'"
          />
          <div class="content-container" v-if="isReady">
            <div class="header" v-if="exams.description !== null">
              <p class="titles">Deskripsi</p>
              <div class="description" v-html="exams.description"></div>
            </div>
            <div class="cta-area">
              <router-link
                v-if="haveUnfinishedExam"
                :to="`/topic/${this.$route.params.id_topic}/exam/${this.$route.params.id}/page`"
                class="btn btn-blue btn-block"
                >Lanjut {{ exams.is_quiz ? "Quiz" : "Tes" }}</router-link
              >
              <p
                v-else-if="
                  attempt_left != null &&
                  attempt_left != undefined &&
                  attempt_left == 0
                "
                :style="{
                  textAlign: 'center',
                }"
              >
                Kamu sudah mencapai batas percobaan
              </p>
              <a v-else @click="submitPoin()" class="btn btn-blue btn-block">{{
                isLoading
                  ? "Loading..."
                  : exams.is_quiz
                  ? "Mulai Quiz"
                  : "Mulai Tes"
              }}</a>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-fade-transition>
</template>

<script>
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import Header from "@/components/HeaderApp";
import { isMobile } from "@/helpers/deviceDetect";
import NavbarFullDesktop from "@/components/NavbarFullDesktop.vue";

export default {
  name: "CourseExamRules",
  metaInfo() {
    return {
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
  data() {
    return {
      exams: [],
      id_topic: "",
      timeExam: "",
      haveUnfinishedExam: false,
      attempt_left: 0,
      isMobile: false,
      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: true,
    };
  },
  methods: {
    submitPoin() {
      this.isLoading = true;
      var data = {
        exam_id: this.$route.params.id,
        poin: 0,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/exam-poin/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            setTimeout(() => {
              this.$router.push(
                `/topic/${this.$route.params.id_topic}/exam/${this.$route.params.id}/page`
              );
              this.isLoading = false;
            }, 3000);
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getExam(id) {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.exams = res.data.data.exam;
            this.attempt_left = this.exams.attempt_left;
            // Check if there are any exam attempts
            if (this.exams.exam_poin_user.length > 0) {
              // Sort attempts by latest first
              const sortedAttempts = this.exams.exam_poin_user.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at)
              );
              const latestAttempt = sortedAttempts[0];

              // Check if the latest attempt is unfinished
              if (latestAttempt.finished_at === null) {
                this.haveUnfinishedExam = true;
              } else {
                this.haveUnfinishedExam = false;
              }
            } else {
              this.haveUnfinishedExam = false;
            }
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
    getTimeExam() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam-config`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.timeExam = res.data.data.ExamConfig.time_exam;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    toTime(totalMinutes) {
      const hours = Math.floor(totalMinutes / 60);
      const minutes = totalMinutes % 60;

      if (minutes == 0) {
        return `${hours} jam`;
      } else {
        return `${hours} jam ${minutes} menit`;
      }
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
  async mounted() {
    this.isMobile = isMobile();
    var id = this.$route.params.id;
    this.id_topic = this.$route.params.id_topic;
    if (this.$root.token()) {
      if (id) {
        await this.getTimeExam();
        await this.getExam(id);
      }
    }
  },
};
</script>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;
  position: relative;

  #content-area {
    overflow: auto;
    padding-bottom: 80px;
  }

  .cta-area {
    background: #ffffff;
    box-shadow: 0px -16px 40px rgba(112, 144, 176, 0.2);
    padding: 18px 24px;
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9;
  }

  .cert-area {
    padding: 20px 24px;
    background: rgba(0, 86, 210, 0.1);
    border-radius: 10px;
    margin-bottom: 16px;

    .t-head {
      font-weight: 700;
      font-size: 16px;
      line-height: 19px;
      margin-bottom: 8px;

      color: #0056d2;
    }

    .desc {
      font-weight: 400;
      font-size: 12px;
      color: rgba(43, 44, 39, 0.75);
    }
  }

  .header-title {
    position: relative;
    padding: 10px 0 24px;
    text-align: center;

    .icon {
      position: absolute;
      left: 0;
      top: 6px;
      background: #ffffff;
      box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
      border-radius: 10px;
      padding: 8px;

      img {
        height: 18px;
        margin-bottom: -4px;
      }
    }

    .namepage {
      white-space: nowrap;
      font-weight: 700;
      font-size: 20px;
      width: 70%;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: auto;
    }
  }

  .name {
    font-weight: 700;
    font-size: 20px;
    margin-bottom: 6px;
  }

  .create {
    font-weight: 600;
    font-size: 14px;

    span {
      color: #0056d2;
    }
  }

  .information {
    margin: 8px 0 24px;
    display: flex;
    align-items: center;

    .item {
      display: flex;
      align-items: center;
      margin-right: 24px;

      .v-icon {
        color: rgba(43, 44, 39, 0.5);
        font-size: 16px;
      }

      .label {
        color: rgba(43, 44, 39, 0.5);
        font-size: 12px;
        padding-top: 2px;
        margin-left: 6px;
      }
    }
  }

  .heading {
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 16px;
  }

  .tutor-area {
    display: flex;
    align-items: center;
    padding-bottom: 30px;

    .img-area {
      img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
      }
    }

    .detail {
      margin-left: 15px;

      .name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 6px;
      }

      .job {
        font-weight: 400;
        font-size: 10px;
        opacity: 0.5;
        line-height: 10px;
      }

      a {
        font-weight: 500;
        font-size: 10px;
      }
    }
  }

  .desc-area {
    margin-top: 24px;
    padding-bottom: 8px;

    .description {
      font-size: 12px;
      opacity: 0.75;

      ul {
        margin-top: 16px;

        li {
          margin-bottom: 10px;
          font-size: 12px;
        }
      }
    }
  }
}

.main-content {
  max-width: 100%;
  padding: 0;
  gap: 24px;
  background: white;

  .content-container {
    display: flex;
    flex-direction: column;
    padding: 26px 244px 26px 244px;
    gap: 32px;
    height: 90vh;
    justify-content: space-between;
    align-items: center;

    .header {
      display: flex;
      flex-direction: column;
      color: var(--primary-scale-blue-800-primary, #0056d2);
      font-family: Sora;
      font-size: 32px;
      font-style: normal;
      font-weight: 600;
      line-height: 150%;
      letter-spacing: -0.384px;
      gap: 40px;

      .titles {
        display: flex;
        margin: 0;
        justify-content: center;
        text-transform: uppercase;
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
</style>
