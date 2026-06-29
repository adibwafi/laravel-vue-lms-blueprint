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
        <div class="header-title">
          <a @click="goToHome()">
            <div class="icon">
              <img src="@/assets/img/icn-back.png" alt="" />
            </div>
          </a>
          <div class="namepage">{{ courses.name }}</div>
        </div>
        <!-- Banner Section -->
        <div class="banner">
          <img :src="banner" :alt="courses.name" />
        </div>
        <!-- Course Info Section -->
        <div class="create">
          Dirancang oleh
          <span>{{ courses.tutor.map((t) => t.name).join(", ") }}</span>
        </div>
        <!-- Tabs Section -->
        <div class="tabs-area">
          <div class="tabs active">Kursus</div>
          <router-link :to="`/course/${$route.params.id}/about`" class="tabs"
            >Informasi
          </router-link>
        </div>
        <!-- Progress Section -->
        <div class="progress-area">
          <div class="desc">
            <div class="label">Selesaikan kelasmu</div>
            <div class="progress">
              {{ courses.course_finish }}
              <span>/{{ courses.course_total }}</span>
            </div>
          </div>
          <v-progress-linear
            :value="
              Math.round((courses.course_finish / courses.course_total) * 100)
            "
            color="#0056d2"
            background-color="#D8E3E7"
            rounded
          ></v-progress-linear>
        </div>
        <!-- Course Area Section -->
        <div class="course-area">
          <div
            class="cursor"
            @click="courseisPaid(itm, idx)"
            v-for="(itm, idx) in courses.course"
            :key="idx"
          >
            <div class="lesson-card" v-if="isPretestCourse(itm)">
              <div class="content">
                <div class="number">
                  <span>1</span>
                </div>
                <div class="detail">
                  <div class="name">{{ itm.name }}</div>
                  <div class="time" v-if="isPretestCourse(itm)">
                    {{ toTime(timeExam) }}
                  </div>
                </div>
              </div>
              <div class="status">
                <img
                  src="@/assets/img/status-lock.png"
                  alt=""
                  v-if="courseLocked(itm, idx)"
                />
                <img
                  src="@/assets/img/status-check.png"
                  alt=""
                  v-else-if="itm.is_completed == true"
                />
                <img src="@/assets/img/status-play.png" alt="" v-else />
              </div>
            </div>
            <div class="course-card" v-else>
              <div class="content">
                <div class="name">{{ itm.name }}</div>
                <div class="course">{{ itm.lesson_total }} Topics</div>
                <div class="tutor">
                  by {{ itm.tutor.map((t) => t.name).join(", ") }}
                </div>
              </div>
              <div class="status" v-if="courseLocked(itm, idx)">
                <img src="@/assets/img/course-lock.svg" />
              </div>
              <div class="progress" v-else>
                <v-progress-circular
                  :rotate="360"
                  :size="70"
                  :width="10"
                  :value="
                    Math.round(
                      ((itm.lesson_finish + itm.exam_finish) /
                        (itm.lesson_total + itm.exam_total)) *
                        100
                    )
                  "
                  background-color="#E3ECFA"
                  color="#0056D2"
                >
                  {{
                    percent(
                      ((itm.lesson_finish + itm.exam_finish) /
                        (itm.lesson_total + itm.exam_total)) *
                        100
                    )
                  }}%
                </v-progress-circular>
              </div>
            </div>
          </div>
        </div>
        <!-- Call to Action Section -->
        <div class="cta-area">
          <div v-if="isSubscribe && !courses.is_completed">
            <a @click="continueCourse()" class="btn btn-blue btn-block"
              >Lanjut Belajar</a
            >
          </div>
          <div v-else-if="courses.user_certificate.length > 0">
            <router-link
              :to="`/certificate/${courses.user_certificate[0].id}`"
              class="btn btn-blue btn-block"
              >Lihat Sertifikat</router-link
            >
          </div>
          <div
            v-else-if="
              (courses.type === 'prakerja' || courses.type === 'msib') &&
              !isSubscribe
            "
          >
            <v-row align="center">
              <v-col cols="6">
                <div class="pay-area">
                  <div class="desc-pay">Harga Kursus</div>
                  <div class="amount">
                    {{
                      courses.price == 0
                        ? "GRATIS"
                        : `Rp ${setRp(courses.price)},-`
                    }}
                  </div>
                </div>
              </v-col>
              <v-col cols="6">
                <router-link to="/redeem" class="btn btn-blue padsm btn-block"
                  >Redeem Voucher</router-link
                >
              </v-col>
            </v-row>
          </div>
        </div>
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
        <!-- Desktop Sidebar -->
        <SidebarDesktop />
        <!-- Main Content for Desktop -->
        <v-col cols="9.5" class="main-content">
          <NavbarDesktop :title="'Kelas'" :link="'/home'" />
          <div class="content-container" v-if="isReady">
            <div class="header">
              <p class="titles">{{ courses.name }}</p>
              <div
                v-if="courses?.description"
                class="description"
                v-html="courses?.description"
              ></div>
              <div v-if="courses.type !== 'prakerja'">
                <div class="areas-wrapper">
                  <div class="learn-area">
                    <div class="heading-desktop">
                      Apa yang akan kamu pelajari?
                    </div>
                    <div class="list" v-for="(i, idx) in what_learn" :key="idx">
                      <v-icon color="#0056D2">mdi-check</v-icon>
                      <span>{{ i }}</span>
                    </div>
                  </div>
                  <div
                    class="req-area"
                    v-if="requirements && requirements.length > 0"
                  >
                    <div class="heading-desktop">Persyaratan</div>
                    <ul>
                      <li v-for="item in requirements" :key="item">
                        {{ item }}
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="skill-area">
                  <div class="heading-desktop">
                    Keterampilan yang akan kamu dapat
                  </div>
                  <div class="skill-list">
                    <div class="skill" v-for="item in skill" :key="item">
                      {{ item }}
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <div class="skill-area" v-if="prasyarat.length">
                  <div class="heading-desktop">
                    Prasyarat & Kriteria Pelatihan
                  </div>
                  <div class="list" v-for="(i, idx) in prasyarat" :key="idx">
                    <v-icon color="#0056D2">mdi-check</v-icon>
                    <p class="check-description">{{ i }}</p>
                  </div>
                </div>
                <div v-if="metode_absensi.length" class="skill-area">
                  <div class="heading-desktop">Metode Absensi</div>
                  <div
                    class="list"
                    v-for="(i, idx) in metode_absensi"
                    :key="idx"
                  >
                    <v-icon color="#0056D2">mdi-check</v-icon>
                    <p class="check-description">{{ i }}</p>
                  </div>
                </div>

                <div class="desc-area" v-if="courses.durasi_pelatihan">
                  <div class="heading-desktop">Durasi Program Pelatihan</div>
                  <div
                    class="description"
                    v-html="courses.durasi_pelatihan"
                  ></div>
                </div>
              </div>
            </div>
            <div class="progress-area" v-if="isSubscribe">
              <div class="desc">
                <div class="label">Selesaikan kelasmu</div>
                <div class="progress">
                  {{ courses.course_finish }}
                  <span>/{{ courses.course_total }}</span>
                </div>
              </div>
              <v-progress-linear
                :value="
                  Math.round(
                    (courses.course_finish / courses.course_total) * 100
                  )
                "
                color="#0056d2"
                background-color="#D8E3E7"
                rounded
              ></v-progress-linear>
              <v-card
                class="desktop-program-card"
                v-if="
                  courses.user_certificate.length > 0 && courses.is_completed
                "
                style="margin-top: 32px"
              >
                <v-row align="center">
                  <v-col cols="6">
                    <div class="pay-area">
                      <div class="amount">Kelas selesai</div>
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <router-link
                      :to="`/certificate/${courses.user_certificate[0].id}`"
                      class="button-sertifikat"
                      >Lihat Sertifikat</router-link
                    >
                  </v-col>
                </v-row>
              </v-card>
            </div>
            <v-row fluid v-if="isSubscribe">
              <v-col
                cols="12"
                class="cursor"
                @click="courseisPaid(itm, idx)"
                v-for="(itm, idx) in courses.course"
                :key="idx"
              >
                <v-card class="desktop-program-card">
                  <v-img class="img" src="@/assets/img/img-atom-program.png" />
                  <div class="content">
                    <div class="header">
                      <div class="left">
                        <div class="content-left">
                          <div class="content-row">
                            <div class="heading" v-if="isPretestCourse(itm)">
                              {{ toTime(timeExam) }}
                            </div>
                            <div
                              class="divider"
                              v-if="isPretestCourse(itm)"
                            ></div>
                            <p class="heading" v-if="isPretestCourse(itm)">
                              {{ itm.exam_total }} Exam
                            </p>
                            <p class="heading" v-else>
                              {{ itm.lesson_total }} Lesson
                            </p>
                          </div>

                          <p class="left-title">{{ itm.name }}</p>
                        </div>
                        <div class="modul" v-if="!isPretestCourse(itm)">
                          by {{ itm.tutor.map((t) => t.name).join(", ") }}
                        </div>
                      </div>

                      <div class="status" v-if="courseLocked(itm, idx)">
                        <v-btn class="button-daftar" disabled
                          >Mulai Belajar</v-btn
                        >
                      </div>
                      <div class="status" v-else-if="itm.is_completed == true">
                        <v-btn class="button-selesai">Selesai</v-btn>
                      </div>
                      <div
                        class="status"
                        v-else-if="
                          itm.is_completed == false && itm.course_finish == !0
                        "
                      >
                        <v-btn class="button-lanjut">Lanjut Belajar</v-btn>
                      </div>
                      <div v-else>
                        <v-btn
                          class="button-daftar"
                          v-if="
                            itm.is_unjuk_keterampilan == '1' &&
                            !itm.is_unjuk_keterampilan_open
                          "
                          disabled
                          >Mulai Belajar</v-btn
                        >
                        <v-btn class="button-daftar" v-else
                          >Mulai Belajar</v-btn
                        >
                      </div>
                    </div>
                    <div class="bottom" v-if="itm.link_zoom == !null">
                      <p class="heading">Live Session:</p>
                      <div class="inner-bottom">
                        <v-img
                          class="img-icn"
                          src="@/assets/img/icn-zoom.png"
                        />
                        <div class="modul-bold">Link Zoom:</div>
                        <div class="modul">{{ itm.link_zoom }}</div>
                      </div>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>
            <div v-else-if="courses.type === 'prakerja' || 'msib'">
              <v-card class="desktop-program-card">
                <v-row align="center">
                  <v-col cols="6">
                    <div class="pay-area">
                      <div class="desc-pay">Harga Kursus</div>
                      <div class="amount">
                        {{
                          courses.price == 0
                            ? "GRATIS"
                            : `Rp ${setRp(courses.price)},-`
                        }}
                      </div>
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <router-link
                      to="/redeem"
                      class="btn btn-blue padsm btn-block"
                      >Redeem Voucher</router-link
                    >
                  </v-col>
                </v-row>
              </v-card>
            </div>
            <div v-else>
              <v-card class="desktop-program-card" v-if="courses.price > 0">
                <v-row align="center">
                  <v-col cols="6">
                    <div class="pay-area">
                      <div class="desc-pay">Harga Kursus</div>
                      <div class="amount">Rp{{ setRp(courses.price) }},-</div>
                    </div>
                  </v-col>
                  <v-col cols="6">
                    <a
                      :href="courses.link_payment"
                      target="_blank"
                      class="btn btn-blue padsm btn-block"
                      >Beli Kursus</a
                    >
                  </v-col>
                </v-row></v-card
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
import SidebarDesktop from "@/components/SidebarDesktop.vue";
import NavbarDesktop from "@/components/NavbarDesktop.vue";
import { isMobile } from "@/helpers/deviceDetect";
export default {
  name: "CategoryDetailPage",
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
    SidebarDesktop,
    NavbarDesktop,
  },
  data() {
    return {
      userId: "",
      id: "",
      courses: "",
      lessons: [],
      isSubscribe: false,
      isMobile: false,
      nextCourse: "#",
      idCourse: "",
      timeExam: "",
      title: "",
      Kelas: "",
      redeemCode: "",
      what_learn: "",
      skill: "",
      requirements: "",
      prasyarat: "",
      metode_absensi: "",

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
    };
  },
  computed: {
    completedCourseIndex() {
      return this.courses.course_finish;
    },
  },
  methods: {
    getTimeExam() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam-config`,
        })
        .then((res) => {
          // console.log("time", res);
          if (!res.data.error) {
            this.timeExam = res.data.data.ExamConfig.time_exam;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    isPretestCourse(itm) {
      const name = itm.name.toLowerCase().trim();
      return name === "pretest" || name === "pre-test" || name === "pre test";
    },
    courseLocked(itm, idx) {
      return (
        (!this.isSubscribe && itm.isPaid == 1) ||
        itm.status === "disabled" ||
        this.completedCourseIndex < idx
      );
    },
    getCourse(id) {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/course-category/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.courses = res.data?.data?.CourseCategory;

            this.what_learn = JSON.parse(this.courses.what_learn);
            this.skill = JSON.parse(this.courses.skill);
            this.prasyarat = JSON.parse(this.courses.prasyarat);
            this.metode_absensi = JSON.parse(this.courses.metode_absensi);
            this.requirements = this.courses.requirements
              ? JSON.parse(this.courses.requirements)
              : "";

            // Urutkan kursus berdasarkan order
            this.courses?.course.sort((a, b) => a.order - b.order);

            // Cari apakah ada kursus pretest dengan order 1
            const pretestCourseIndex = this.courses?.course.findIndex(
              (course) => course.order === 1 && this.isPretestCourse(course)
            );

            // Jika ada kursus pretest dengan order 1, pindahkan ke urutan pertama
            if (pretestCourseIndex !== -1) {
              const pretestCourse = this.courses?.course.splice(
                pretestCourseIndex,
                1
              )[0];
              this.courses?.course.unshift(pretestCourse);
            }
            this.banner = `${process.env.VUE_APP_STORAGE_BASE_URL}${this.courses.banner}`;
            this.isLoading = false;
            this.nextCourse =
              window.localStorage.getItem(
                `lastPlayedCourse-${this.userId}-${this.id}`
              ) || this.courses?.course[0]?.id;
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
    readCourse(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/user-course-category/read/${id}`,
        })
        .then((res) => {
          // console.log("Ini Course:", res);
          if (res.data.success) {
            this.isSubscribe = true;
            this.redeemCode = res.data.data.UserCoursesCategory.redeem_code;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    subcribeCourse() {
      this.isLoading = true;
      var data = {
        course_category_id: this.$route.params.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/user-course-category/create`,
          data: data,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            this.isSubscribe = true;
            this.isLoading = false;
            if (this.idCourse) {
              setTimeout(() => {
                this.$router.push(`/topic/${this.idCourse}`);
              }, 1000);
            }
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    prakerjaOAuth(order, redirect_uri, redeem_code) {
      this.isLoading = true;
      // console.log("Redeem Code:", this.redeemCode);
      let data;
      data = {
        token: redeem_code,
        sequence: order,
        redirect_uri: `${process.env.VUE_APP_API_BASE_URL}/topic/${redirect_uri}`,
      };
      // Check if sequence is 1 or greater than or equal to 1000
      if (order >= 1000) {
        this.$router.push(`/topic/${redirect_uri}`);
      }

      this.$root
        .axios({
          method: "post",
          url: "/v1/voucher/redeem",
          data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            if (res.data.data.attendance_status === 1) {
              this.$router.push(`/topic/${redirect_uri}`);
            }
            if (res.data.data.url) {
              setTimeout(() => {
                window.location.href = res.data.data.url;
              }, 2000);
            }
          } else {
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    goToHome() {
      this.$router.push({
        path: `/home`,
      });
    },
    continueCourse() {
      // console.log(this.courses.course);
      // console.log("Ini Continuecourse: " + this.nextCourse);
      let itm = this.courses.course.find((item) => {
        return item.id == this.nextCourse;
      });
      localStorage.setItem(
        "prakerjaData",
        JSON.stringify({
          course_id: itm.id,
          course_name: itm.name,
          sequence: itm.order,
          is_uk: itm.is_unjuk_keterampilan,
          redeem_code: this.redeemCode,
        })
      );
      if (this.courses.type === "prakerja") {
        this.prakerjaOAuth(this.redeemCode);
        this.$router.push({
          path: `/topic/${this.nextCourse}`,
        });
        return;
      } else {
        this.$router.push({
          path: `/topic/${this.nextCourse}`,
        });
      }
    },
    courseisPaid(itm, idx) {
      // console.log("Ini Courseispaid:");
      if (this.courseLocked(itm, idx)) {
        return;
      }
      // Save Data Prakerja
      localStorage.setItem(
        "prakerjaData",
        JSON.stringify({
          course_id: itm.id,
          course_name: itm.name,
          sequence: itm.order,
          is_uk: itm.is_unjuk_keterampilan,
          redeem_code: this.redeemCode,
        })
      );
      if (this.courses.type === "prakerja") {
        this.prakerjaOAuth(itm.order, itm.id, this.redeemCode);
      } else {
        if (itm.isPaid == 0) {
          if (this.isSubscribe == true) {
            this.$router.push(`/topic/${itm.id}`);
          } else {
            if (this.courses.price > 0) {
              this.$router.push(`/topic/${itm.id}`);
            } else {
              this.idCourse = itm.id;
              this.subcribeCourse();
            }
          }
        } else {
          if (this.isSubscribe == true) {
            this.$router.push(`/topic/${itm.id}`);
          }
        }
      }
    },
    getTime(time) {
      var minutes = Math.floor(time / 60);
      var seconds = time - minutes * 60;

      return `${minutes} min ${seconds} sec`;
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
    setRp(val) {
      let value = (val / 1).toFixed(0).replace(".", ",");
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
    percent(i) {
      return Math.round(Math.round(i * 100) / 100);
    },
  },
  async mounted() {
    this.isMobile = isMobile();
    var id = this.$route.params.id;
    this.id = id;
    this.userId = this.$root.userId();
    if (this.$root.token()) {
      if (id) {
        await this.getCourse(id);
        await this.readCourse(id);
        await this.getTimeExam();
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

    .pay-area {
      .desc-pay {
        color: #2b2c27;
        opacity: 0.5;
        font-size: 12px;
      }

      .amount {
        font-weight: 700;
        font-size: 1.25rem;
      }
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

  .banner {
    margin-bottom: 24px;

    img {
      border-radius: 20px;
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
    margin-bottom: 24px;

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

  .tabs-area {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border-radius: 10px;
    padding: 8px;
    margin-bottom: 24px;

    .tabs {
      font-weight: 600;
      font-size: 14px;
      text-align: center;
      width: 50%;
      padding: 18px;
      border-radius: 10px;
      background: transparent;
      // transition: all 0.4s ease-in-out;
      cursor: pointer;
      color: #2b2c27 !important;
    }

    .tabs.active {
      color: #fff !important;
      background: #0056d2;
    }
  }

  .progress-area {
    .desc {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 12px;

      .label {
        font-weight: 600;
        font-size: 13px;
      }

      .progress {
        font-weight: 600;
        font-size: 13px;

        span {
          font-weight: 400;
          opacity: 0.5;
        }
      }
    }
  }

  .course-area {
    margin-top: 24px;
    padding-bottom: 4.5rem;

    .course-card {
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
      border-radius: 15px;

      .content {
        width: 80%;

        .course {
          font-weight: 400;
          font-size: 12px;
          opacity: 0.75;
          padding: 2px 0;
          color: #bdbdbd;
        }

        .name {
          font-weight: 700;
          font-size: 18px;
          color: #2b2c27;
          margin: 0;
        }

        .tutor {
          font-weight: 400;
          font-size: 10px;
          opacity: 0.75;
          color: #bdbdbd;
        }
      }

      .status {
        img {
          width: 100%;
          height: 100%;
        }
      }
    }
  }

  .lesson-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;

    .content {
      display: flex;
      align-items: center;

      .number {
        span {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: #0056d2;
          font-weight: 600;
          font-size: 14px;
          color: #fff;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }

      .number.grey {
        background: transparent !important;

        span {
          color: #0056d2;
          background: #d8e3e7;
        }
      }

      .detail {
        margin-left: 12px;

        .name {
          font-size: 16px;
          margin-bottom: 0;
          color: #2b2c27;
        }

        .time {
          font-size: 12px;
          color: #bdbdbd;
        }
      }
    }

    .status {
      img {
        width: 32px;
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
    padding: 26px 56px 26px 56px;
    gap: 32px;

    .pay-area {
      .desc-pay {
        color: #2b2c27;
        opacity: 0.5;
        font-size: 18px;
      }

      .amount {
        font-size: 30px;
        font-weight: 700;
      }
    }

    .button-redeem {
      height: 44px;
      padding: 4px 20px;
      border-radius: 4px;
      background: #0056d2;
      color: white !important;
      text-transform: capitalize;
      font-size: 14px;
      font-weight: 600;
    }

    .header {
      display: flex;
      flex-direction: column;
      gap: 26px;
      letter-spacing: -0.6px;

      .titles {
        display: flex;
        margin: 0;
        font-weight: 600;
        font-size: 40px;
      }

      .description {
        margin: 0px;
        color: var(--Black-Scale-Black-700, #3d3d3d);
        font-size: 16px;
        font-weight: 400;
        line-height: 180%;
      }

      .list {
        margin-bottom: 10px;

        .v-icon {
          font-size: 18px;
        }

        span {
          padding-left: 8px;
          font-size: 16px;
          color: var(--Black-Scale-Black-700, #3d3d3d);
        }

        .check-description {
          display: inline;
          font-size: 16px;
          color: var(--Black-Scale-Black-700, #3d3d3d);
          margin-left: 0.5rem;
        }
      }

      .show {
        font-weight: 700;
        font-size: 12px;
        color: #0056d2;
        cursor: pointer;

        .v-icon {
          font-size: 18px;
        }
      }

      .heading-desktop {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 16px;
      }

      /* Wrap .learn-area, .skill-area, .req-area */
      .areas-wrapper {
        display: flex;
        flex-direction: row;
        gap: 32px;
        margin-bottom: 32px;

        /* Ensure all children have the same max-width */
        > div {
          flex-basis: 0;
          flex-grow: 1;
          max-width: 100%;
        }

        .learn-area {
          .list {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;

            .v-icon {
              font-size: 18px;
            }

            span {
              padding-left: 8px;
              font-size: 14px;
              color: var(--Black-Scale-Black-700, #3d3d3d);
            }
          }
        }

        .req-area {
          ul {
            li {
              margin-bottom: 10px;
              font-size: 16px;
              color: var(--Black-Scale-Black-700, #3d3d3d);
            }
          }
        }
      }

      .skill-area {
        padding-bottom: 30px;

        .skill-list {
          .skill {
            font-size: 14px;
            padding: 5px 16px;
            background: #ffffff;
            border-radius: 10px;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
          }
        }
      }
    }

    .tabs {
      display: flex;
      flex-direction: row;
      gap: 32px;
      border-bottom: 1px solid rgba(102, 102, 102, 0.24);

      .bar {
        color: var(--Black-Scale-Black-900, #121212);
        font-family: Sora;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 180%;
      }
    }

    .desktop-program-card {
      display: flex;
      border-radius: 8px;
      box-shadow: 0px 4px 24px 0px rgba(18, 18, 18, 0.08);
      padding: 24px 24px 26px 24px;
      gap: 24px;

      .img {
        width: 187px;
        max-width: 187px;
        height: 187px;
      }

      .img-icn {
        width: 24px;
        max-width: 24px;
        height: 24px;
      }

      .content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 32px;
        width: 100%;

        .header {
          display: flex;
          flex-direction: row;
          justify-content: space-between;
          align-items: flex-end;

          .left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            width: 100%;

            .content-left {
              display: flex;
              flex-direction: column;
              gap: 8px;

              .content-row {
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 4px;

                .divider {
                  width: 2px;
                  height: 2px;
                  background: var(--Black-Scale-Black-500, #666);
                }
              }

              .left-title {
                display: flex;
                color: var(--Black-Scale-Black-900, #121212);
                font-size: 20px;
                font-style: normal;
                font-weight: 500;
                line-height: 28px;
                letter-spacing: -0.3px;
                margin: 0;
              }
            }
          }

          .description {
            display: flex;
            color: var(--Black-Scale-Black-500, #666);
            font-size: 12px;
            font-weight: 400;
            line-height: 180%;
            margin-bottom: 0 !important;
          }
        }

        .bottom {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: flex-start;
          gap: 8px;
          align-self: stretch;

          .inner-bottom {
            display: flex;
            flex-direction: row;
            gap: 8px;
          }
        }
      }

      .heading {
        color: var(--Black-Scale-Black-500, #666);
        font-size: 12px;
        font-style: normal;
        font-weight: 500;
        line-height: 180%;
        margin: 0;
        margin-bottom: 0;
      }

      .modul {
        color: var(--Black-Scale-Black-500, #666);
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 180%;
        margin: 0;
      }

      .modul-bold {
        color: var(--Black-Scale-Black-900, #121212);
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: 180%;
        margin: 0;
      }

      .button-daftar {
        height: 44px;
        padding: 4px 20px;
        border-radius: 4px;
        background: #0056d2;
        color: white !important;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }

      .button-selesai {
        height: 44px;
        display: flex;
        padding: 4px 20px;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        background: #00993d;
        color: white !important;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }

      .button-lanjut {
        height: 44px;
        display: flex;
        padding: 4px 20px;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        background: #fdb72b;
        color: #121212 !important;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }
    }
  }
}

.button-sertifikat {
  height: 44px;
  display: flex;
  padding: 4px 20px;
  justify-content: center;
  align-items: center;
  border-radius: 4px;
  background: #00993d;
  color: white !important;
  text-transform: capitalize;
  font-size: 14px;
  font-weight: 600;
}

.desktop-course-card .description {
  margin-bottom: 0 !important;
}

.v-tab {
  text-transform: none !important;
}
</style>
