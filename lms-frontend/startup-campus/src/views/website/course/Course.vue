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
      <div class="header-home">
        <img src="@/assets/img/icn-logo.png" alt="" class="brand" />
        <router-link to="/home">
          <img src="@/assets/img/icn-notif.png" alt="" class="notif" />
        </router-link>
      </div>
      <div class="tabs-area">
        <div
          class="tabs"
          :class="{ active: tab == 'ongoing' }"
          v-on:click="tabOpen('ongoing')"
        >
          Sedang dipelajari
        </div>
        <div
          class="tabs"
          :class="{ active: tab == 'completed' }"
          v-on:click="tabOpen('completed')"
        >
          Selesai
        </div>
      </div>

      <div class="tab-content" v-if="tab == 'ongoing'">
        <div class="course-area" v-if="courses && courses.length > 0">
          <div v-for="(itm, idx) in filteredData(2)" :key="idx">
            <router-link :to="`/course/${itm.course_category_id}`">
              <div class="course-card" :class="itm.course_category.color">
                <div class="information">
                  <div class="name">{{ itm.course_category.name }}</div>
                  <div class="tutor">
                    oleh
                    {{
                      itm.course_category.tutor.map((t) => t.name).join(", ")
                    }}
                  </div>
                </div>
                <div class="progress">
                  <v-progress-circular
                    :rotate="360"
                    :size="70"
                    :width="10"
                    :value="
                      (itm.course_category.course_finish /
                        itm.course_category.course_total) *
                      100
                    "
                    color="white"
                  >
                    {{
                      Math.round(
                        (itm.course_category.course_finish /
                          itm.course_category.course_total) *
                          100
                      )
                    }}%
                  </v-progress-circular>
                </div>
              </div>
            </router-link>
          </div>
        </div>
        <div class="no-course" v-if="filteredData(2).length < 1">
          <img src="@/assets/img/img-no-course.png" alt="" />
          <h5>Aduh, kamu belum ambil kelas ya?</h5>
          <p>
            Buruan tuntasin segera semua kelasnya, biar kamu semakin pede di
            dunia digital!
          </p>
        </div>
      </div>
      <div class="tab-content" v-if="tab == 'completed'">
        <div class="course-area" v-if="courses && courses.length > 0">
          <div v-for="(itm, idx) in filteredData(1)" :key="idx">
            <router-link :to="`/course/${itm.course_category_id}`">
              <div class="course-card" :class="itm.course_category.color">
                <div class="information">
                  <div class="name">{{ itm.course_category.name }}</div>
                  <div class="tutor">
                    oleh
                    {{
                      itm.course_category.tutor.map((t) => t.name).join(", ")
                    }}
                  </div>
                </div>
                <div class="progress">
                  <v-progress-circular
                    :rotate="360"
                    :size="70"
                    :width="10"
                    :value="
                      (itm.course_category.course_finish /
                        itm.course_category.course_total) *
                      100
                    "
                    color="white"
                  >
                    {{
                      (itm.course_category.course_finish /
                        itm.course_category.course_total) *
                      100
                    }}%
                  </v-progress-circular>
                </div>
              </div>
            </router-link>
          </div>
        </div>
        <div class="no-course" v-if="filteredData(1).length < 1">
          <img src="@/assets/img/img-no-course.png" alt="" />
          <h5>Aduh, kamu belum ambil kelas ya?</h5>
          <p>
            Buruan tuntasin segera semua kelasnya, biar kamu semakin pede di
            dunia digital!
          </p>
        </div>
      </div>
      <div class="main-menu">
        <div class="menu-area">
          <router-link to="/home" class="list">
            <img src="@/assets/img/icn-home.png" alt="" />
            <div class="name">Home</div>
          </router-link>
          <router-link to="/my-course" class="list active">
            <img src="@/assets/img/icn-course-ac.png" alt="" />
            <div class="name">My Course</div>
          </router-link>
          <router-link to="/profile" class="list">
            <img src="@/assets/img/icn-profile.png" alt="" />
            <div class="name">Profile</div>
          </router-link>
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
          <NavbarDesktop :title="'Course Management'" />
          <div class="content-container" v-if="isReady">
            <div class="header">
              <p class="titles">Kelas</p>
              <v-tabs v-model="tab" class="tabs">
                <v-tab
                  class="bar"
                  v-for="(itm, idx) in types"
                  :key="idx"
                  :value="itm.type"
                  @click="handleChangeType(itm.type)"
                >
                  {{ itm.label }}
                </v-tab>
              </v-tabs>
            </div>
            <v-row fluid>
              <v-col
                cols="12"
                class="cursor"
                v-for="(itm, idx) in categories"
                :key="idx"
              >
                <v-card class="desktop-program-card">
                  <v-img class="img" src="@/assets/img/img-atom-program.png" />
                  <div class="content">
                    <div class="header">
                      <div class="left">
                        <div class="content-left">
                          <div class="content-row">
                            <p class="heading-blue">
                              {{ itm.type }}
                            </p>
                          </div>
                          <p class="left-title">
                            {{ itm.name }}
                          </p>
                          <div class="content-row">
                            <div class="modul">
                              oleh
                              {{ itm.tutor.map((t) => t.name).join(", ") }}
                            </div>
                          </div>
                        </div>
                      </div>
                      <router-link :to="`/course/${itm.id}`">
                        <v-btn
                          class="button-selesai"
                          v-if="itm.is_completed === true"
                          >Lihat Seritifkat</v-btn
                        >
                        <v-btn
                          class="button-lanjut"
                          v-if="
                            itm.course_finish > 0 && itm.is_completed === false
                          "
                          >Lanjut Belajar</v-btn
                        >
                        <v-btn
                          class="button-daftar"
                          v-if="itm.course_finish === 0"
                          >Mulai Belajar</v-btn
                        >
                      </router-link>
                    </div>
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
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import SidebarDesktop from "@/components/SidebarDesktop.vue";
import NavbarDesktop from "@/components/NavbarDesktop.vue";
import { isMobile } from "@/helpers/deviceDetect";
export default {
  name: "MyCoursePage",
  metaInfo() {
    return {
      title: "My Course - AcmeLMS",
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
    Header,
    Alert,
    SidebarDesktop,
    NavbarDesktop,
  },
  data: () => ({
    isMobile: false,
    tab: "ongoing",
    course: [
      {
        id: "123",
        title: "Product Market Fit",
        tutor: "Achmad Zaky",
        course: "The Founder",
        percent: "80",
        color: "red",
      },
      {
        id: "2345",
        title: "Computer Vision",
        tutor: "Korniawan Prabowo",
        course: "Artificial Intelligence",
        percent: "100",
        color: "purple",
      },
      {
        id: "5677",
        title: "Data Science",
        tutor: "Ainun Najib",
        course: "Data Storytelling",
        percent: "80",
        color: "green",
      },
      {
        id: "8494204",
        title: "Setup & Basic Python",
        tutor: "Raymond Christopher",
        course: "Back-end Engineer",
        percent: "100",
        color: "blue",
      },
    ],
    types: [
      // {
      //   label: "Semua",
      //   type: "all",
      // },
      {
        label: "Prakerja",
        type: "prakerja",
      },
      {
        label: "Public Bootcamp",
        type: "public_bootcamp",
      },
      {
        label: "MSIB",
        type: "msib",
      },
    ],
    courses: [],
    page: 1,
    totalPage: 1,
    itemsPerPage: 8,

    // notif
    isLoading: false,
    notifsuccess: false,
    notifdanger: false,
    textNotif: "",
    isReady: false,
  }),
  watch: {
    page() {
      this.getType(this.$route.query.type);
    },
  },
  methods: {
    tabOpen(tab) {
      if (tab === this.tab) {
        this.tab = "ongoing";
      } else {
        this.tab = tab;
      }
    },
    handleChangeType(type, toggle) {
      const currentType = this.$route.query.type || ""; // Get the current query parameter value
      if (type !== currentType) {
        // Check if the new type is different from the current one
        this.page = 1;
        this.$router.push({ query: { type: type } });
        this.getType(this.$route.query.type);
      }
      toggle();
    },

    getCategory() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/user-course-category`,
        })
        .then((res) => {
          // console.log(res);
          // console.log("🚀 ~ file: Course.vue:223 ~ .then ~ console", console);
          if (res.data.success) {
            this.isReady = true;
            this.courses = res.data.data.UserCoursesCategory.rows;
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
          this.isLoading = false;
          this.loadScreen = false;
        });
    },

    getType(type = "prakerja") {
      var page = this.page;
      var itemsPerPage = this.itemsPerPage;
      type = this.$route.query.type ?? "prakerja";
      this.type = type;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/course-category?page=${page}&limit=${itemsPerPage}&type=${type}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.totalPage = res.data.data.CourseCategorys.total_page;
            this.categories = res.data.data.CourseCategorys.rows;
            // console.log(this.categories);
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

    filteredData(val) {
      if (val == 1) {
        return this.courses.filter((item) => {
          return (
            item.course_category.course_finish /
              item.course_category.course_total ==
            1
          );
        });
      } else {
        return this.courses.filter((item) => {
          return (
            item.course_category.course_finish /
              item.course_category.course_total !=
            1
          );
        });
      }
    },
  },
  mounted() {
    if (this.$root.token()) {
      this.isMobile = isMobile();
      this.getCategory();
      this.getType();
    } else {
      this.$root.redirectLogin();
    }
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;
  padding-bottom: 5rem;

  .course-area {
    .course-card {
      background: linear-gradient(96.65deg, #7401fc 0%, #b80df9 100%);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 25px;
      color: #fff;
      margin-bottom: 20px;

      .information {
        .course {
          font-weight: 400;
          font-size: 12px;
        }

        .name {
          font-weight: 700;
          font-size: 18px;
          padding: 2px 0;
        }

        .tutor {
          font-weight: 400;
          font-size: 10px;
          opacity: 0.75;
        }
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
      color: #2b2c27;
    }

    .tabs.active {
      color: #fff;
      background: #0056d2;
    }
  }

  .no-course {
    text-align: center;
    padding-top: 150px;

    img {
      width: 150px;
    }

    h5 {
      font-weight: 700;
      font-size: 16px;
      padding-top: 12px;
      padding-bottom: 8px;
    }

    p {
      font-weight: 400;
      font-size: 13px;
      color: rgba(43, 44, 39, 0.5);
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
          align-items: center;

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

      .heading-blue {
        color: var(--primary-scale-blue-800-primary, #0056d2);
        font-family: Sora;
        font-size: 10px;
        font-style: normal;
        font-weight: 500;
        line-height: 180%;
        text-transform: uppercase;
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

.v-tab {
  text-transform: none !important;
}

.header-home {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0 24px;

  .brand {
    width: 30px;
  }

  .notif {
    width: 30px;
  }
}

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

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
  background: linear-gradient(96.65deg, #a71e34 0%, #e01e37 100%) !important;
}
</style>
