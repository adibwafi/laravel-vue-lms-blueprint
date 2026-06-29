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
        <!-- Header Title -->
        <div class="header-title">
          <a @click="goToCategoryDetail()">
            <div class="icon">
              <img src="@/assets/img/icn-back.png" alt="" />
            </div>
          </a>
          <div class="namepage">{{ courses.name }}</div>
        </div>
        <!-- Banner -->
        <div class="banner">
          <img :src="banner" :alt="courses.name" />
        </div>
        <!-- Course Tutor -->
        <div class="create" v-if="courses.tutor">
          Dirancang oleh
          <span>{{ courses.tutor.map((t) => t.name).join(", ") }}</span>
        </div>
        <!-- Information -->
        <div class="information">
          <div class="item" v-if="!isPrakerja">
            <v-icon>mdi-star-outline</v-icon>
            <div class="label">
              {{ courses.rating == 0 ? "-" : percent(courses.rating) }}
            </div>
          </div>
          <div class="item">
            <v-icon>mdi-book-open-variant</v-icon>
            <div class="label">{{ courses.lesson_total }} Materi</div>
          </div>
          <div class="item">
            <v-icon>mdi-sticker-text</v-icon>
            <div class="label">{{ courses.exam_total }} Tes</div>
          </div>
        </div>
        <!-- Zoom Link -->
        <div v-if="courses.link_zoom">
          <a :href="courses.link_zoom" target="_blank">
            <button class="button-zoom">Link Zoom</button>
          </a>
        </div>
        <!-- Tabs Area -->
        <div class="tabs-area">
          <div class="tabs active">Materi</div>
          <router-link :to="`/topic/${$route.params.id}/about`" class="tabs"
            >Informasi
          </router-link>
        </div>
        <!-- Exam Area -->
        <div class="exam-area" v-if="exams && exams.length > 0">
          <div class="progress-area">
            <div class="desc">
              <div class="label">Selesaikan tesmu</div>
              <div class="progress">
                {{ courses.exam_finish }}<span>/{{ courses.exam_total }}</span>
              </div>
            </div>
            <v-progress-linear
              :value="(courses.exam_finish / courses.exam_total) * 100"
              color="#0056d2"
              background-color="#D8E3E7"
              rounded
            ></v-progress-linear>
          </div>
          <div class="label">Tes</div>
          <a @click="goExam(itm)" v-for="(itm, idx) in exams" :key="idx">
            <div class="exam-card">
              <div class="content">
                <div class="detail">
                  <div class="name">{{ itm.name }}</div>
                  <div class="time">{{ toTime(timeExam) }}</div>
                  <div class="information">
                    <div v-if="itm.kkm != null">KKM: {{ itm.kkm }}</div>
                    <div v-if="itm.kkm != null && itm.exam_done">|</div>
                    <div v-if="itm.highest_grade !== null">
                      Nilai: {{ Math.round(itm.highest_grade) }}
                    </div>
                    <div v-if="itm.exam_done && itm.attempt_left != null">
                      |
                    </div>
                    <div
                      v-if="
                        !itm.exam_done &&
                        itm.attempt_left != null &&
                        itm.kkm != null
                      "
                    >
                      |
                    </div>
                    <div v-if="itm.attempt_left != null">
                      Sisa attempt: {{ itm.attempt_left }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="status">
                <img
                  src="@/assets/img/status-check.png"
                  alt=""
                  v-if="itm.exam_done"
                />
                <img src="@/assets/img/status-play.png" alt="" v-else />
              </div>
            </div>
          </a>
        </div>
        <!-- Lesson Area -->
        <div class="lesson-area">
          <a
            v-for="(itm, idx) in lessons"
            :key="idx"
            @click="goLesson(itm, idx)"
          >
            <div class="lesson-card">
              <div class="content">
                <div class="number">
                  <span>{{ itm.sorter }}</span>
                </div>
                <div class="detail">
                  <div class="name">{{ itm.name }}</div>
                  <div class="time" v-if="itm.using_zoom">
                    Zoom | {{ getTime(itm.learn_time, true) }}
                  </div>
                  <div class="time" v-else-if="itm.is_quiz">
                    Quiz {{ getQuizInformation(itm) }}
                  </div>
                  <div class="time" v-else>{{ getTime(itm.learn_time) }}</div>
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
                  v-else-if="itm.is_completed == 'true'"
                />
                <img src="@/assets/img/status-play.png" alt="" v-else />
              </div>
            </div>
          </a>
        </div>
        <!-- CTA Area -->
        <div class="cta-area">
          <div v-if="isSubscribe">
            <div v-if="courses.exam_finish / courses.exam_total != 1">
              <a @click="continueLesson()" class="btn btn-blue btn-block"
                >Lanjut Belajar</a
              >
            </div>
            <div v-else>
              <!-- <div v-if="courses.user_certificate.length > 0">
                <router-link
                  :to="`/certificate/${courses.user_certificate[0].id}`"
                  class="btn btn-blue btn-block"
                  >Lihat Sertifikat</router-link
                >
              </div> -->
              <div>
                <div v-if="this.exams.length > 0 && !this.exams[0].exam_done">
                  <a @click="goExam(exams[0])" class="btn btn-blue btn-block"
                    >Mulai Ujian</a
                  >
                </div>
                <div class="text-center" v-else>
                  Kamu sudah menyelesaikan tes ini
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <a @click="subcribeCourse()" class="btn btn-blue btn-block"
              >Mulai Belajar</a
            >
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
      <v-row fluid>
        <!-- Desktop Sidebar -->
        <SidebarDesktop />
        <!-- Main Content for Desktop -->
        <v-col cols="9.5" class="main-content" v-if="isReady">
          <NavbarDesktop
            :title="navbarTitle"
            :link="`/course/${this.courses.categories_id}`"
          />
          <div class="content-container">
            <div class="header">
              <p class="titles">{{ courses.name }}</p>
              <div class="banner">
                <img :src="banner" :alt="courses.name" />
              </div>
              <div class="create" v-if="courses.tutor">
                Dirancang oleh
                <span>{{ courses.tutor.map((t) => t.name).join(", ") }}</span>
              </div>
            </div>
            <div v-if="exams && exams.length > 0">
              <div class="progress-area-desktop">
                <div class="desc">
                  <div class="label">Selesaikan tesmu</div>
                  <div class="progress">
                    {{ courses.exam_finish
                    }}<span>/{{ courses.exam_total }}</span>
                  </div>
                </div>
                <v-progress-linear
                  :value="(courses.exam_finish / courses.exam_total) * 100"
                  color="#0056d2"
                  background-color="#D8E3E7"
                  rounded
                ></v-progress-linear>
              </div>
              <v-row fluid>
                <v-col
                  cols="12"
                  class="cursor"
                  @click="goExam(itm)"
                  v-for="(itm, idx) in exams"
                  :key="idx"
                >
                  <v-card class="desktop-program-card">
                    <v-img
                      class="img"
                      src="@/assets/img/img-atom-program.png"
                    />
                    <div class="content">
                      <div class="header">
                        <div class="left">
                          <div class="content-left">
                            <p class="left-title">{{ itm.name }}</p>
                            <div class="heading">
                              {{ toTime(timeExam) }}
                              <div class="information">
                                <div v-if="itm.kkm != null">
                                  KKM: {{ itm.kkm }}
                                </div>
                                <div v-if="itm.kkm != null && itm.exam_done">
                                  |
                                </div>
                                <div v-if="itm.highest_grade !== null">
                                  Nilai: {{ Math.round(itm.highest_grade) }}
                                </div>
                                <div
                                  v-if="
                                    itm.exam_done && itm.attempt_left != null
                                  "
                                >
                                  |
                                </div>
                                <div
                                  v-if="
                                    !itm.exam_done &&
                                    itm.attempt_left != null &&
                                    itm.kkm != null
                                  "
                                >
                                  |
                                </div>
                                <div v-if="itm.attempt_left != null">
                                  Sisa attempt: {{ itm.attempt_left }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="status" v-if="itm.exam_done">
                          <v-btn class="button-selesai">Selesai</v-btn>
                        </div>
                        <div class="status" v-else-if="!itm.exam_done">
                          <v-btn @click="goExam(exams[0])" class="button-lanjut"
                            >Lanjut Belajar</v-btn
                          >
                        </div>
                        <v-btn class="button-daftar" v-else
                          >Mulai Belajar</v-btn
                        >
                      </div>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
            </div>
            <div v-else class="lesson-area">
              <div class="progress-area-desktop">
                <div class="desc">
                  <div class="label">Selesaikan kelasmu</div>
                  <div class="progress">
                    {{ courses.lesson_finish
                    }}<span>/{{ courses.lesson_total }}</span>
                  </div>
                </div>
                <v-progress-linear
                  :value="(courses.lesson_finish / courses.lesson_total) * 100"
                  color="#0056d2"
                  background-color="#D8E3E7"
                  rounded
                ></v-progress-linear>
              </div>
              <v-row fluid>
                <v-col
                  cols="12"
                  class="cursor"
                  v-for="(itm, idx) in lessons"
                  :key="idx"
                  @click="goLesson(itm, idx)"
                >
                  <v-card class="desktop-lesson-card">
                    <v-img
                      class="img"
                      src="@/assets/img/img-atom-program.png"
                    />
                    <div class="content">
                      <div class="header">
                        <div class="left">
                          <div class="content-left">
                            <div class="content-row">
                              <div class="heading">
                                {{ getTime(itm.learn_time, true) }}
                              </div>
                              <div
                                class="divider"
                                v-if="itm.using_zoom || itm.is_quiz"
                              ></div>
                              <p class="heading" v-if="itm.using_zoom">Zoom</p>
                              <p class="heading" v-else-if="itm.is_quiz">
                                Quiz {{ getQuizInformation(itm) }}
                              </p>
                            </div>

                            <p class="left-title">{{ itm.name }}</p>
                          </div>
                        </div>
                        <v-btn
                          class="button-daftar"
                          disabled
                          v-if="courseLocked(itm, idx)"
                          >Mulai Belajar</v-btn
                        >
                        <div
                          class="status"
                          v-else-if="itm.is_completed == 'true'"
                        >
                          <v-btn class="button-selesai">Selesai</v-btn>
                        </div>
                        <div
                          class="status"
                          v-else-if="itm.is_completed == false"
                        >
                          <v-btn class="button-lanjut">Lanjut Belajar</v-btn>
                        </div>
                        <v-btn class="button-daftar" v-else
                          >Mulai Belajar</v-btn
                        >
                      </div>
                      <div
                        class="bottom"
                        v-if="itm.using_zoom && courses.link_zoom"
                      >
                        <p class="heading">Live Session:</p>
                        <a :href="courses.link_zoom">
                          <div class="inner-bottom">
                            <v-img
                              class="img-icn"
                              src="@/assets/img/icn-zoom.png"
                            />
                            <div class="modul-bold">Link Zoom</div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
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
  name: "CourseDetailPage",
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
      courses: [],
      lessons: [],
      exams: [],
      courseCategory: {},
      isPrakerja: false,
      banner: null,
      isSubscribe: false,
      timeExam: "",
      startExam: false,
      isMobile: false,
      // idExam: "",

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
      prakerjaData: null,
    };
  },
  computed: {
    nextLesson() {
      return (
        this.lessons[this.completedLessonIndex] ?? this.lessons[0] ?? undefined
      );
    },
    completedLessonIndex() {
      return this.courses.lesson_finish;
    },
    navbarTitle() {
      if (this.exams && this.exams.length > 0) {
        return "Exam";
      } else {
        return "Lesson";
      }
    },
  },
  methods: {
    getQuizInformation(item) {
      let output = "";
      if (item.exam && item.exam.highest_grade != null) {
        output += `| Nilai: ${item.exam.highest_grade.toFixed(1)}`;
      }
      if (item.exam && item.exam.kkm != null) {
        output += `| KKM: ${item.exam.kkm}`;
      }
      if (item.exam && item.exam.attempt_left != null) {
        output += `| Sisa Attempt: ${item.exam.attempt_left}`;
      }
      return output;
    },
    courseLocked(course, idx) {
      return this.completedLessonIndex < idx;
    },
    getCourse(id) {
      this.loadScreen = true;
      this.isLoading = true;
      return this.$root
        .axios({
          method: "get",
          url: `/v1/course/read/${id}`,
        })
        .then((res) => {
          // console.log("course detail", res);
          if (res.data.success) {
            this.isReady = true;
            this.courses = res.data.data.Course;
            // console.log("Lesson Finish:", this.courses.lesson_finish);
            // console.log("Lesson Total:", this.courses.lesson_total);
            this.banner = `${process.env.VUE_APP_STORAGE_BASE_URL}${this.courses.banner}`;
            this.courseCategory = this.courses.course_category;
            this.isPrakerja = this.courseCategory.type == "prakerja";
            this.isLoading = false;
            window.localStorage.setItem(
              `lastPlayedCourse-${this.userId}-${res.data.data.Course.categories_id}`,
              id
            );
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
    completeLesson(course, lesson) {
      var data = {
        courses_id: course.id,
        lessons_id: lesson.id,
      };
      this.isLoading = true;
      return this.$root
        .axios({
          method: "post",
          url: `/v1/user-lesson/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            lesson.is_completed = "true";
            this.courses.lesson_finish += 1;

            this.alert(
              "Selamat, kamu sudah menyelesaikan tes ini 🥳",
              "success"
            );
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
    getLesson(id) {
      this.isLoading = true;
      return this.$root
        .axios({
          method: "get",
          url: `/v1/lesson?courses_id=${id}`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.lessons = res.data.data.Lessons.rows;
            // console.log("Rows:", this.lessons);
            this.lessons.forEach((lesson) => {
              // untuk ByPass user yang stuck gabisa akses
              // this.completeLesson(this.courses, lesson);

              if (lesson.is_quiz) {
                if (
                  lesson.exam?.exam_done &&
                  lesson.current_user_lesson?.length == 0
                ) {
                  this.completeLesson(this.courses, lesson);
                }
              }
            });
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
    getExam(id) {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/exam?course_id=${id}`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.exams = res.data.data.exam.rows;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
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
    readCourse(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/user-course/read/${id}`,
        })
        .then((res) => {
          // console.log("read", res);
          if (res.data.success) {
            this.isSubscribe = true;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },
    goLesson(itm, idx) {
      if (this.courseLocked(itm, idx)) {
        return;
      }

      if (itm.using_zoom) {
        if (this.isSubscribe == false) {
          this.subcribeCourse();
        }

        // // Memulai penundaan 30 menit sebelum mengaktifkan completeLesson
        // setTimeout(() => {
        //   if (itm.is_completed === "false") {
        //     this.completeLesson(this.courses, itm);
        //   }
        // }, 30 * 60 * 1000);

        // ByPass completeLesson setelah klik zoom
        if (itm.is_completed === "false") {
          this.completeLesson(this.courses, itm);
        }

        // Langsung membuka jendela Zoom
        window.open(this.courses.link_zoom);
        return;
      }

      if (itm.is_quiz) {
        if (this.isSubscribe == false) {
          this.subcribeCourse();
        }
        this.$router.push(
          `/topic/${this.$route.params.id}/exam/${itm.exam.id}`
        );
        return;
      }

      // Set local storage prakerja berdasarkan cek scope dan is_completed
      if (itm.scope) {
        const prakerjaItem = {
          lesson_id: itm.id,
          lesson_name: itm.name,
          is_completed: itm.is_completed,
          is_feedbacked: itm.is_feedbacked,
          is_journal_reflection: itm.is_journal_reflection,
          scope: itm.scope,
          answer: itm.user_lesson_answer[0]?.answer || null,
          notes: itm.user_lesson_answer[0]?.notes || null,
        };
        this.prakerjaData = { ...this.prakerjaData, ...prakerjaItem };
        localStorage.setItem("prakerjaData", JSON.stringify(this.prakerjaData));
      }

      if (this.isSubscribe == true) {
        this.$router.push(`/topic/${this.$route.params.id}/lesson/${itm.id}`);
      } else {
        this.subcribeCourse();
        setTimeout(() => {
          this.$router.push(`/topic/${this.$route.params.id}/lesson/${itm.id}`);
        }, 3000);
      }
    },
    subcribeCourse() {
      var data = {
        courses_id: this.$route.params.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/user-course/create`,
          data: data,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.isSubscribe = true;
            this.alert("Selamat mengerjakan", "success");
          }
        })
        .catch((e) => {
          console.log(e);
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
    goToCategoryDetail() {
      this.$router.push({
        path: `/course/${this.courses.categories_id}`,
      });
    },
    continueLesson() {
      this.goLesson(this.nextLesson, this.completedLessonIndex);
    },
    setRp(val) {
      let value = (val / 1).toFixed(0).replace(".", ",");
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
    goExam(itm) {
      if (itm.can_go_to_exam) {
        this.$router.push(`/topic/${this.$route.params.id}/exam/${itm.id}`);
      } else {
        this.$router.push(
          `/topic/${this.$route.params.id}/exam/${itm.id}/score`
        );
      }
    },
    getTime(time) {
      const hours = Math.floor(time / 3600);
      const with_hour = hours > 0 ? true : false;
      if (with_hour) {
        let ret = `${hours} hour`;
        const minutes = Math.floor((time - hours * 3600) / 60);
        ret += minutes > 0 ? ` ${minutes} min` : "";
        return ret;
      } else {
        const minutes = Math.floor(time / 60);
        let ret = `${minutes} min`;

        const seconds = time - minutes * 60;
        ret += seconds > 0 ? ` ${seconds} sec` : "";

        return ret;
      }
    },
    percent(i) {
      return parseFloat(i).toFixed(1);
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
        await this.getLesson(id);
        await this.getExam(id);
        await this.readCourse(id);
        await this.getTimeExam();
        this.prakerjaData = JSON.parse(localStorage.getItem("prakerjaData"));
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
        font-size: 22px;
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
    margin-bottom: 40px;

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
    padding-bottom: 12px;

    .desc {
      display: flex;
      flex-direction: row;
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

  .exam-area {
    margin-top: -16px;
    padding-bottom: 12px;

    .label {
      font-weight: 600;
      font-size: 13px;
      padding-bottom: 12px;
    }

    .exam-card {
      background: #fff;
      border: 2px solid #0056d2;
      border-radius: 15px;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;

      .content {
        display: flex;
        flex: 90%;
        width: 100%;
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
          width: 100%;

          .name {
            font-size: 16px;
            margin-bottom: 0;
            color: #0056d2;
          }

          .time {
            font-size: 12px;
            color: #0056d2;
          }

          .information {
            display: flex;
            gap: 5px;
            margin-bottom: 0;
            color: #0056d2;
            font-size: 12px;

            .check {
              color: #67da57;
              font-size: 1.2rem;
            }
          }
        }
      }

      .status {
        line-height: 0px;

        img {
          width: 32px;
          line-height: 0px;
        }
      }
    }
  }

  .button-zoom {
    /* Auto layout */

    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    padding: 0px 20px;
    gap: 8px;

    width: 115px;
    height: 44px;

    /* Primary Scale/Blue 50 */

    background: #b4cefb;
    border-radius: 40px;

    /* Inside auto layout */

    flex: none;
    order: 3;
    flex-grow: 0;

    /* Button/Small */
    margin: 1.75rem 0px;

    font-family: "Sora";
    font-style: normal;
    font-weight: 600;
    font-size: 14px;
    line-height: 44px;
    /* identical to box height, or 314% */

    /* Primary/500 */

    color: #0b63e5;
  }

  .button-zoom:hover {
    filter: brightness(85%);
    transition: 0.7s;
  }

  .lesson-area {
    margin-top: 24px;
    padding-bottom: 4.5rem;

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
        padding-right: 15px;

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
        line-height: 0px;

        img {
          width: 32px;
          line-height: 0px;
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
    padding: 26px 56px 26px 56px;
    gap: 32px;

    .header {
      display: flex;
      flex-direction: column;
      color: #1e1e1e;
      font-family: Sora;
      font-size: 40px;
      font-style: normal;
      font-weight: 600;
      line-height: 150%;
      letter-spacing: -0.6px;
      gap: 8px;

      .create {
        font-weight: 600;
        font-size: 14px;

        span {
          color: #0056d2;
        }
      }

      .banner {
        display: flex;
        margin-bottom: 0px;
        max-width: 300px;
        height: auto;

        img {
          border-radius: 20px;
        }
      }

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

      .description.more {
        -webkit-line-clamp: 1000;
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
      border-radius: 15px;
      border: 2px solid #0056d2;
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

              .information {
                display: flex;
                gap: 5px;
                margin-bottom: 0;
                color: #0056d2;
                font-size: 12px;

                .check {
                  color: #67da57;
                  font-size: 1.2rem;
                }
              }

              .left-title {
                display: flex;
                color: #0056d2;
                font-size: 20px;
                font-style: normal;
                font-weight: 700;
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
        color: #0056d2;
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
        padding: 0px 20px;
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

    .desktop-lesson-card {
      display: flex;
      border-radius: 15px;
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

              .information {
                display: flex;
                gap: 5px;
                margin-bottom: 0;
                color: #0056d2;
                font-size: 12px;

                .check {
                  color: #67da57;
                  font-size: 1.2rem;
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
        padding: 0px 20px;
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

.progress-area-desktop {
  padding-bottom: 24px;

  .desc {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
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
</style>
