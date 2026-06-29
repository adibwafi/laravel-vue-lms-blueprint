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
          <img src="@/assets/img/grad-hat.png" alt="" />
        </div>
        <div class="heading">Lesson Completed!</div>
        <div class="desc">
          Please leave a review<br />
          for this lesson
        </div>
        <v-rating
          v-model="rating"
          color="#FDB72B"
          background-color="#E0E0E0"
          empty-icon="$ratingFull"
          size="40"
          hover
        ></v-rating>
        <hr />
        <div v-if="rating > 0">
          <div class="reason-area" v-if="rating < 4">
            <v-btn-toggle
              v-model="reason"
              color="#0056D2"
              group
              multiple
              rounded
            >
              <!-- Rating buruk -->
              <v-btn value="Sulit"> Sulit </v-btn>
              <v-btn value="Membingungkan"> Membingungkan </v-btn>
              <v-btn value="Terlalu Mudah"> Terlalu Mudah </v-btn>
              <v-btn value="Tidak Berguna"> Tidak Berguna </v-btn>
              <v-btn value="Membosankan"> Membosankan </v-btn>
            </v-btn-toggle>
          </div>
          <div class="reason-area" v-else>
            <v-btn-toggle
              v-model="reason"
              color="#0056D2"
              group
              multiple
              rounded
            >
              <!-- Rating baik -->
              <v-btn value="Seru"> Seru </v-btn>
              <v-btn value="Mudah dimengerti"> Mudah dimengerti </v-btn>
              <v-btn value="Interaktif"> Interaktif </v-btn>
              <v-btn value="Cerita menarik"> Cerita menarik </v-btn>
              <v-btn value="Applicable"> Applicable </v-btn>
            </v-btn-toggle>
          </div>
          <hr />
        </div>
        <div class="cta">
          <button
            class="btn btn-blue btn-block"
            @click="submit()"
            :disabled="buttonDisabled || addLoading"
          >
            {{ addLoading ? "Loading..." : "Selesai" }}
          </button>
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
        <!-- Main Content for Desktop -->
        <v-col cols="12" class="main-content">
          <NavbarFullDesktop :title="`Rating`" />
          <div class="content-container">
            <div class="rating-area">
              <div class="imgs">
                <img src="@/assets/img/grad-hat.png" alt="" />
              </div>
              <div class="heading">Lesson Completed!</div>
              <div class="desc">
                Please leave a review<br />
                for this lesson
              </div>
              <v-rating
                v-model="rating"
                color="#FDB72B"
                background-color="#E0E0E0"
                empty-icon="$ratingFull"
                size="40"
                hover
              ></v-rating>
              <hr />
              <div v-if="rating > 0">
                <div class="reason-area" v-if="rating < 4">
                  <v-btn-toggle
                    v-model="reason"
                    color="#0056D2"
                    group
                    multiple
                    rounded
                  >
                    <!-- Rating buruk -->
                    <v-btn value="Sulit"> Sulit </v-btn>
                    <v-btn value="Membingungkan"> Membingungkan </v-btn>
                    <v-btn value="Terlalu Mudah"> Terlalu Mudah </v-btn>
                    <v-btn value="Tidak Berguna"> Tidak Berguna </v-btn>
                    <v-btn value="Membosankan"> Membosankan </v-btn>
                  </v-btn-toggle>
                </div>
                <div class="reason-area" v-else>
                  <v-btn-toggle
                    v-model="reason"
                    color="#0056D2"
                    group
                    multiple
                    rounded
                  >
                    <!-- Rating baik -->
                    <v-btn value="Seru"> Seru </v-btn>
                    <v-btn value="Mudah dimengerti"> Mudah dimengerti </v-btn>
                    <v-btn value="Interaktif"> Interaktif </v-btn>
                    <v-btn value="Cerita menarik"> Cerita menarik </v-btn>
                    <v-btn value="Applicable"> Applicable </v-btn>
                  </v-btn-toggle>
                </div>
                <hr />
              </div>
              <div class="cta">
                <button
                  class="btn btn-blue btn-block"
                  @click="submit()"
                  :disabled="buttonDisabled || addLoading"
                >
                  {{ addLoading ? "Loading..." : "Selesai" }}
                </button>
              </div>
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
  name: "LessonPageRating",
  metaInfo() {
    return {
      title: "Lesson Rating - AcmeLMS",
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
    buttonDisabled() {
      return this.rating < 1 || this.reason.length < 1;
    },
  },
  data() {
    return {
      id: "",
      rating: 0,
      reason: [],
      isMobile: false,

      // notif
      addLoading: false,
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
    };
  },
  watch: {
    rating: {
      handler() {
        this.reason = [];
      },
    },
  },
  methods: {
    submit() {
      this.addLoading = true;
      var reason = this.reason.join(", ");
      var data = {
        lessons_id: this.id,
        rating: this.rating,
        feedback: reason,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/lesson-rating/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert("Review berhasil disimpan", "success");
            setTimeout(() => {
              this.$router.replace({
                path: `/topic/${this.$route.params.id_topic}`,
              });
            }, 1000);
          }
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
    this.isMobile = isMobile();
    var id = this.$route.params.id;
    if (this.$root.token()) {
      if (id) {
        this.id = id;
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
}

.main-content {
  max-width: 100%;
  padding: 0;
  gap: 24px;
  background: white;

  .content-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 26px 244px 26px 244px;
    height: 90vh;

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

.rating-area {
  text-align: center;

  .imgs {
    text-align: center;
    padding-top: 30px;

    img {
      width: 182px;
    }
  }

  .heading {
    padding: 50px 0 24px;
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
    padding-bottom: 23px;
  }

  hr {
    margin: 30px 0 40px;
    opacity: 0.3;
  }

  .cta {
    margin-top: 24px;
    margin-bottom: 24px;
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
