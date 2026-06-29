<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <Header />
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <div v-if="isReady">
        <div class="header-title">
          <a @click="$router.go(-1)">
            <div class="icon">
              <img src="@/assets/img/icn-back.png" alt="" />
            </div>
          </a>
          <div class="namepage">{{ courses.name }}</div>
        </div>
        <div class="information">
          <div class="item">
            <div class="label">
              {{ courses.rating == 0 ? "-" : courses.rating }}
            </div>
            <v-icon>mdi-star-outline</v-icon>
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
        <div class="cert-area" v-if="courses.exam_total > 0">
          <div class="t-head">Sertifikat Tersedia</div>
          <div class="desc">
            Kamu bisa mendapatkan sertifikat kegiatan jika menyelesaikan tes
            yang akan terbuka setelah semua topik diselesaikan
          </div>
        </div>
        <div v-if="courses.course_category.type !== 'prakerja'">
          <div class="tutor-area">
            <div class="img-area">
              <img :src="tutor_img" alt="" />
            </div>
            <div class="detail">
              <div class="name">{{ courses.tutor.name }}</div>
              <div class="job">{{ courses.tutor.job }}</div>
              <a
                :href="courses.tutor.link_profile"
                target="_blank"
                rel="noopener noreferrer"
                >Lihat profil</a
              >
            </div>
          </div>
          <div class="learn-area">
            <div class="heading">Apa yang akan kamu pelajari?</div>
            <div class="list" v-for="(i, idx) in what_learn" :key="idx">
              <v-icon color="#0056D2">mdi-check</v-icon>
              <span>{{ i }}</span>
            </div>
          </div>
        </div>
        <div class="desc-area">
          <div class="heading">Deskripsi</div>
          <div
            class="description"
            :class="{ more: isMore }"
            v-html="courses.description"
          ></div>
          <div class="show" @click="isMore = !isMore" v-if="showMore">
            Perlihatkan {{ isMore ? "lebih sedikit" : "lebih banyak" }}
            <v-icon color="#0056D2">mdi-chevron-down</v-icon>
          </div>
        </div>
        <div v-if="courses.course_category.type !== 'prakerja'">
          <div class="skill-area">
            <div class="heading">Keterampilan yang akan kamu dapat</div>
            <div class="skill-list">
              <div class="skill" v-for="item in skill" :key="item">
                {{ item }}
              </div>
            </div>
          </div>
          <div class="req-area" v-if="requirements && requirements.length > 0">
            <div class="heading">Persyaratan</div>
            <ul>
              <li v-for="item in requirements" :key="item">
                {{ item }}
              </li>
            </ul>
          </div>
        </div>
        <div v-else>
          <div class="skill-area">
            <div class="heading">Prasyarat & Kriteria Pelatihan</div>
            <div
              class="list"
              v-for="(i, idx) in JSON.parse(courses.course_category.prasyarat)"
              :key="idx"
            >
              <table>
                <tr>
                  <td class="check-column">
                    <v-icon class="check" color="#0056D2">mdi-check</v-icon>
                  </td>
                  <td class="description-column">
                    <span class="description">{{ i }}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="skill-area">
            <div class="heading">Metode Absensi</div>
            <div
              class="list"
              v-for="(i, idx) in JSON.parse(
                courses.course_category.metode_absensi
              )"
              :key="idx"
            >
              <table>
                <tr>
                  <td class="check-column">
                    <v-icon class="check" color="#0056D2">mdi-check</v-icon>
                  </td>
                  <td class="description-column">
                    <span class="description">{{ i }}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="desc-area">
            <div class="heading">Durasi Program Pelatihan</div>
            <div
              class="description"
              v-html="courses.course_category.durasi_pelatihan"
            ></div>
          </div>
          <div class="desc-area">
            <div class="heading">Tenaga Pelatih</div>
            <div
              class="tutor-area"
              v-for="(tutor, idx) in courses.course_category.tutor"
              :key="idx"
            >
              <div class="img-area">
                <img :src="`${storageBaseUrl}${tutor.image}`" alt="" />
              </div>
              <div class="detail">
                <div class="name">{{ tutor.name }}</div>
                <div class="job">{{ tutor.job }}</div>
                <a
                  :href="tutor.link_profile"
                  target="_blank"
                  rel="noopener noreferrer"
                  >Lihat profil</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/LoadingFull";
import Alert from "@/components/Alert.vue";
export default {
  name: "CourseDetailAboutPage",
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
  },
  data() {
    return {
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      courses: [],
      what_learn: "",
      skill: "",
      requirements: "",
      isMore: false,
      tutor_img: null,
      showMore: true,

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
    };
  },
  methods: {
    getCourse(id) {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/course/read/${id}`,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.isReady = true;
            var course = res.data.data.Course;
            if (course.description.length < 400) {
              this.showMore = false;
            }
            this.courses = course;
            this.what_learn = JSON.parse(course.what_learn);
            this.skill = JSON.parse(course.skill);
            this.requirements = course.requirements
              ? JSON.parse(course.requirements)
              : "";
            this.tutor_img = `${process.env.storageBaseUrl}${this.courses.tutor.image}`;
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
    var id = this.$route.params.id;
    if (this.$root.token()) {
      if (id) {
        this.getCourse(id);
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
      width: 65%;

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

  .learn-area {
    padding-bottom: 30px;

    .list {
      display: flex;
      align-items: flex-start;
      margin-bottom: 10px;

      .v-icon {
        font-size: 18px;
      }

      span {
        padding-left: 8px;
        font-size: 12px;
        opacity: 0.75;
      }
    }
  }

  .list {
    .check-column {
      vertical-align: top;
    }

    .description-column {
      padding-left: 0.5rem;
      vertical-align: top;
    }

    .description {
      font-size: 12px;
      font-weight: 400;
      color: #2b2c27bf;
    }
  }

  .desc-area {
    padding-bottom: 30px;

    .description {
      font-size: 12px;
      opacity: 0.75;
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
      margin-top: 4px;
      cursor: pointer;

      .v-icon {
        font-size: 18px;
      }
    }
  }

  .skill-area {
    padding-bottom: 30px;

    .skill-list {
      .skill {
        font-size: 12px;
        padding: 5px 16px;
        background: #ffffff;
        border-radius: 10px;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
      }
    }
  }

  .req-area {
    padding-bottom: 30px;

    ul {
      li {
        margin-bottom: 10px;
        font-size: 12px;
        opacity: 0.75;
      }
    }
  }
}
</style>
