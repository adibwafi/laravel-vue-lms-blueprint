<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div v-if="isMobile" class="main-container">
      <Header />
      <div class="header-title">
        <a @click="$router.go(-1)">
          <img src="@/assets/img/icn-back.png" alt="" class="icon" />
        </a>
        <div class="namepage">Kursus</div>
      </div>
      <v-row v-if="loadScreen">
        <v-col cols="6" v-for="i in 12" :key="i">
          <v-skeleton-loader type="image"></v-skeleton-loader>
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col cols="6" v-for="(itm, idx) in courses" :key="idx">
          <router-link :to="`/course/${itm.id}`">
            <div class="course-card" :class="itm.color">
              <div class="titles">{{ itm.name }}</div>
              <div class="tutor">
                oleh {{ itm.tutor.map((t) => t.name).join(", ") }}
              </div>
              <div class="progress-area">
                <div class="count">
                  {{ itm.course_finish }} / {{ itm.course_total }}
                </div>
                <v-progress-linear
                  color="#fff"
                  height="8"
                  :value="(itm.course_finish / itm.course_total) * 100"
                  rounded
                ></v-progress-linear>
              </div>
            </div>
          </router-link>
        </v-col>
      </v-row>
    </div>
    <div v-else>
      <v-row>
        <!-- Desktop Sidebar -->
        <SidebarDesktop />
        <!-- Main Content for Desktop -->
        <v-col cols="9.5" class="main-content">
          <NavbarDesktop :title="'Course Management'" />
          <div class="content-container">
            <div class="header">
              <p class="titles">Kelas</p>
            </div>
            <v-row v-if="loadScreen">
              <v-col cols="auto" v-for="i in 4" :key="i">
                <v-skeleton-loader type="image"></v-skeleton-loader>
              </v-col>
            </v-row>
            <v-row v-else>
              <v-col cols="auto" v-for="(itm, idx) in courses" :key="idx">
                <v-card
                  class="desktop-course-card"
                  max-width="316"
                  height="440"
                >
                  <v-img class="img" src="@/assets/img/icn-all-level.png" />
                  <v-img
                    class="align-end text-white"
                    height="auto"
                    src="@/assets/img/img-program.png"
                  />
                  <div class="content">
                    <div class="text">
                      <p class="title">{{ itm.name }}</p>
                      <p class="description">Progress kelas</p>
                      <div class="progress-area">
                        <div class="count">
                          {{ itm.course_finish }} / {{ itm.course_total }}
                        </div>
                        <v-progress-linear
                          color="#0056d2"
                          height="8"
                          :value="(itm.course_finish / itm.course_total) * 100"
                          rounded
                        ></v-progress-linear>
                      </div>
                    </div>
                    <router-link :to="`/course/${itm.id}`">
                      <v-btn class="button-daftar">Lihat Kelas</v-btn>
                    </router-link>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import SidebarDesktop from "@/components/SidebarDesktop.vue";
import NavbarDesktop from "@/components/NavbarDesktop.vue";
import { isMobile } from "@/helpers/deviceDetect";
export default {
  name: "CoursePage",
  metaInfo() {
    return {
      title: "Course - AcmeLMS",
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
    SidebarDesktop,
    NavbarDesktop,
  },
  data: () => ({
    loadScreen: false,
    isMobile: false,
    courses: [],
  }),
  methods: {
    getCourse() {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/course-category?limit=999999`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.courses = res.data.data.CourseCategorys.rows;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
          this.loadScreen = false;
        });
    },
  },
  mounted() {
    if (this.$root.token()) {
      this.getCourse();
      this.isMobile = isMobile();
    }
  },
  computed: {},
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;

  .course-card.purple {
    background: linear-gradient(96.65deg, #7401fc 0%, #b80df9 100%) !important;
  }

  .course-card.blue {
    background: linear-gradient(96.65deg, #1e66ee 0%, #6a06f0 100%) !important;
  }

  .course-card.green {
    background: linear-gradient(96.14deg, #19d18b 0%, #03cfce 100%) !important;
  }

  .course-card.orange {
    background: linear-gradient(96.65deg, #e7660b 0%, #faa102 100%) !important;
  }

  .course-card.red {
    background: linear-gradient(96.65deg, #93291e 0%, #ed213a 100%) !important;
  }

  .course-card {
    // background: linear-gradient(96.65deg, #7401fc 0%, #b80df9 100%);
    border-radius: 20px;
    height: 270px;
    color: #fff;
    padding: 24px;
    position: relative;

    .titles {
      font-weight: 700;
      font-size: 16px;
      line-height: 19px;
      padding-bottom: 4px;
    }

    .tutor {
      font-weight: 400;
      font-size: 10px;
      color: rgba(255, 255, 255, 0.75);
    }

    .progress-area {
      width: 60%;
      position: absolute;
      bottom: 24px;
      left: 24px;

      .count {
        font-weight: 400;
        font-size: 13px;
        margin-bottom: 5px;
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
      gap: 26px;
      color: #1e1e1e;
      font-family: Sora;
      font-size: 40px;
      font-style: normal;
      font-weight: 600;
      line-height: 150%;
      letter-spacing: -0.6px;

      .titles {
        display: flex;
        margin: 0;
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

    .desktop-course-card {
      display: flex;
      position: relative;
      flex-direction: column;
      border-radius: 12px;
      border: 1px solid var(--Black-Scale-Black-50, #ccc);

      .img {
        width: 109px;
        height: auto;
        position: absolute;
        right: 189px;
        top: 16px;
        z-index: 2;
      }

      .content {
        display: flex;
        flex-direction: column;
        padding: 16px 16px 18px 16px;
        min-height: 240px;
        justify-content: space-between;

        .text {
          display: flex;
          flex-direction: column;
          gap: 8px;

          .title {
            color: var(--Black-Scale-Black-900, #121212);
            font-size: 24px;
            font-weight: 600;
            line-height: 130%;
            letter-spacing: -0.288px;
            margin: 0;
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

        .text-harga {
          display: flex;
          flex-direction: column;
          margin-bottom: 16px;
        }

        .harga {
          color: var(--Black-Scale-Black-500, #666);
          font-size: 10px;
          font-weight: 500;
          line-height: 180%;
          margin: 0;
        }

        .angka {
          color: var(--primary-scale-blue-800-primary, #0056d2);
          font-size: 14px;
          font-weight: 600;
          line-height: 180%;
          margin: 0;
        }
      }

      .button-daftar {
        width: 100%;
        height: 44px;
        padding: 0px 20px;
        border-radius: 4px;
        background: #0056d2;
        color: white !important;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }

      .button-lihat-kelas {
        width: 100%;
        height: 44px;
        padding: 0px 20px;
        color: #0056d2;
        border-radius: 8px;
        border: 1.5px solid var(--primary-scale-blue-800-primary, #0056d2);
        background: white;
        box-shadow: none;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }
    }
  }
}

.progress-area {
  width: 80%;

  .count {
    font-weight: 400;
    font-size: 13px;
    margin-bottom: 5px;
  }
}

.desktop-course-card .description {
  margin-bottom: 0 !important;
}

.v-tab {
  text-transform: none !important;
}

.header-title {
  display: flex;
  align-items: center;
  padding: 10px 0 24px;

  .icon {
    width: 25px;
  }

  .namepage {
    font-weight: 500;
    font-size: 16px;
    margin-left: 16px;
    padding-bottom: 4px;
  }
}
</style>
