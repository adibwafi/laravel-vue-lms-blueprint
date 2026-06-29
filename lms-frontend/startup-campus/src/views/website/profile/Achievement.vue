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
      <div class="header-home">
        <img src="@/assets/img/icn-logo.png" alt="" class="brand" />
        <router-link to="/home">
          <img src="@/assets/img/icn-notif.png" alt="" class="notif" />
        </router-link>
      </div>
      <div class="titles">Pencapaian</div>
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
                      itm.course_category.tutor?.map((t) => t.name).join(", ")
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
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/LoadingFull";
import Alert from "@/components/Alert.vue";
export default {
  name: "AchievementPage",
  metaInfo() {
    return {
      title: "Achievement - AcmeLMS",
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
  },
  data: () => ({
    tab: "completed",
    courses: [],

    // notif
    isLoading: false,
    notifsuccess: false,
    notifdanger: false,
    textNotif: "",
  }),
  methods: {
    tabOpen(tab) {
      if (tab === this.tab) {
        this.tab = "ongoing";
      } else {
        this.tab = tab;
      }
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
          if (res.data.success) {
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
          this.loadScreen = false;
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
      this.getCategory();
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
        width: 80%;
        padding-right: 1rem;

        .course {
          font-weight: 400;
          font-size: 12px;
        }

        .name {
          font-weight: 700;
          font-size: 1rem;
          padding: 2px 0;
        }

        .tutor {
          font-weight: 400;
          font-size: 10px;
          opacity: 0.75;
        }
      }

      .progress {
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

.titles {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 24px;
}
</style>
