<!-- eslint-disable vue/multi-word-component-names -->

<template>
  <v-fade-transition>
    <div v-if="isMobile" class="main-container">
      <Header />
      <div class="header-home">
        <img src="@/assets/img/icn-logo.png" alt="" class="brand" />
        <router-link to="/home">
          <img src="@/assets/img/icn-notif.png" alt="" class="notif" />
        </router-link>
      </div>
      <div class="heading" v-if="banners && banners.length > 0">
        <h4>Promosi</h4>
        <router-link to="/promotion">Lihat semua</router-link>
      </div>
      <v-skeleton-loader
        type="image"
        v-if="loadScreen"
        class="mb-6"
      ></v-skeleton-loader>
      <carousel
        class="promo-banner"
        :autoplay="true"
        :nav="false"
        :items="1"
        :margin="10"
        v-else-if="!loadScreen && banners && banners.length > 0"
      >
        <a :href="i.url" target="_blank" v-for="(i, idx) in banners" :key="idx">
          <img :src="`${storageBaseUrl}${i.image}`" :alt="i.name" />
        </a>
      </carousel>
      <router-link :to="`/redeem`" class="button-redeem mb-5 mt-1">
        <v-btn class="button-redeem font-weight-bold" color="primary" block>
          Redeem Voucher
        </v-btn>
      </router-link>
      <div class="heading">
        <h4>Kursus Menarik Buat Kamu</h4>
        <router-link to="/course">Lihat semua</router-link>
      </div>
      <v-row class="my-2">
        <v-slide-group mandatory center-active :show-arrows="false">
          <v-slide-item
            v-for="(itm, idx) in types"
            :key="idx"
            v-slot="{ toggle }"
          >
            <v-btn
              outlined
              class="button mr-2 rounded-pill"
              color="primary"
              :class="{ 'v-slide-item--active': type === itm.type }"
              @click="handleChangeType(itm.type, toggle)"
            >
              {{ itm.label }}
            </v-btn>
          </v-slide-item>
        </v-slide-group>
      </v-row>
      <v-row v-if="loadScreen">
        <v-col cols="6" v-for="i in 4" :key="i">
          <v-skeleton-loader type="image"></v-skeleton-loader>
        </v-col>
      </v-row>
      <v-row v-else>
        <v-col cols="6" v-for="(itm, idx) in categories" :key="idx">
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
      <div class="text-center mt-10">
        <v-pagination v-model="page" :length="totalPage" circle></v-pagination>
      </div>
      <div class="main-menu">
        <div class="menu-area">
          <router-link to="/home" class="list active">
            <img src="@/assets/img/icn-home-ac.png" alt="" />
            <div class="name">Home</div>
          </router-link>
          <router-link to="/my-course" class="list">
            <img src="@/assets/img/icn-course.png" alt="" />
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
      <v-row>
        <!-- Desktop Sidebar -->
        <SidebarDesktop />
        <!-- Main Content for Desktop -->
        <v-col cols="9.5" class="main-content">
          <NavbarDesktop :title="'Course Management'" />
          <div class="content-container">
            <div class="header">
              <p class="titles">Dashboard</p>
              <v-tabs v-model="tab" class="tabs">
                <v-tab
                  class="bar"
                  v-for="(itm, idx) in types"
                  :key="idx"
                  :value="itm.type"
                  @click="handleChangeType(itm.type, toggle)"
                >
                  {{ itm.label }}
                </v-tab>
              </v-tabs>
            </div>
            <v-row v-if="banners && banners.length > 0">
              <v-col cols="auto">
                <carousel :autoplay="true" :nav="false" :items="1" :margin="10">
                  <a
                    :href="i.url"
                    target="_blank"
                    v-for="(i, idx) in banners"
                    :key="idx"
                  >
                    <img
                      :src="`${storageBaseUrl}${i.image}`"
                      :alt="i.name"
                      :style="{ maxWidth: '50%', borderRadius: '12px' }"
                    />
                  </a>
                </carousel> </v-col
            ></v-row>
            <v-row v-if="isReady">
              <v-col cols="auto" v-for="(itm, idx) in categories" :key="idx">
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
                      <p
                        class="description"
                        v-html="truncateDescription(itm.description)"
                      ></p>
                    </div>
                    <div class="text-harga">
                      <p class="harga">Harga mulai</p>
                      <p class="angka">{{ formatPrice(itm.price) }},-</p>
                    </div>
                    <router-link :to="`/course/${itm.id}`">
                      <v-btn class="button-daftar">Daftar Kelas</v-btn>
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
import carousel from "vue-owl-carousel";
import Header from "@/components/HeaderApp";
import SidebarDesktop from "@/components/SidebarDesktop.vue";
import NavbarDesktop from "@/components/NavbarDesktop.vue";
import Loading from "@/components/Loading";
import { isMobile } from "@/helpers/deviceDetect";
export default {
  name: "HomePage",
  metaInfo() {
    return {
      title: "Home - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {
    carousel,
    Header,
    SidebarDesktop,
    NavbarDesktop,
    Loading,
  },
  data: () => ({
    banners: [],
    categories: [],
    type: "",
    storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
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
    page: 1,
    totalPage: 1,
    itemsPerPage: 8,
    loadScreen: true,
    isMobile: false,
    drawer: false,
    isLoading: false,
    isReady: false,
    image: "",
    title: "",
    tab: "",
  }),
  watch: {
    page() {
      this.getCategory(this.$route.query.type);
    },
  },
  methods: {
    formatPrice(price) {
      // Format the price as Indonesian Rupiah
      const formattedPrice = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(price);

      // Add the comma after "Rp" and remove the decimals
      const numericPart = formattedPrice.slice(3);
      return `Rp ${numericPart}`;
    },
    truncateDescription(description) {
      const maxLength = 100;
      if (description && description.length > maxLength) {
        return `${description.substring(0, maxLength)}...`;
      }
      return description;
    },
    handleChangeType(type, toggle) {
      const currentType = this.$route.query.type || ""; // Get the current query parameter value
      if (type !== currentType) {
        // Check if the new type is different from the current one
        this.page = 1;
        this.$router.push({ query: { type: type } });
        this.getCategory(this.$route.query.type);
      }
      toggle();
    },

    getBanner() {
      this.isLoading = true;
      this.loadScreen = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/banner-promotion?limit=5`,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.isReady = true;
            this.banners = res.data.data.BannerPromotions.rows;
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

    getCategory(type = "prakerja") {
      var page = this.page;
      var itemsPerPage = this.itemsPerPage;
      type = this.$route.query.type ?? "prakerja";
      this.type = type;
      this.loadScreen = true;
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
          this.loadScreen = false;
        });
    },
  },
  mounted() {
    this.getBanner();
    this.getCategory();
    this.isMobile = isMobile();
  },
  computed: {},
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;
  padding-bottom: 6rem;

  .redeem-area {
    width: 100%;

    a {
      width: 100%;
    }
  }

  .button-redeem {
    width: 100%;
    margin-top: 8px;
    margin-bottom: 24px;
    text-transform: none;
  }

  #redeemText {
    color: white !important;
  }

  .button.v-btn--outlined {
    font-size: 12px;
    padding: 0rem 1rem;
    text-transform: none;
    border-radius: 50%;
    background-color: transparent;
    color: #0b63e5;
    border: 1px solid #0b63e5 !important;
  }

  .v-slide-item--active {
    background-color: #b4cefb !important;
    color: #1e4bb1 !important;
  }

  .heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;

    h4 {
      font-size: 1rem;
    }

    a {
      font-size: 12px;
    }
  }

  .promo-banner {
    padding-bottom: 32px;

    img {
      border-radius: 25px;
    }
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

  .course-card {
    border-radius: 20px;
    min-height: 265px;
    color: #fff;
    padding: 24px;
    display: flex;
    flex-direction: column;

    .titles {
      font-weight: 700;

      @media (max-width: 480px) {
        font-size: 0.8rem;
      }

      font-size: 1rem;
      line-height: 19px;
      padding-bottom: 4px;
      vertical-align: top;
    }

    .tutor {
      font-weight: 400;
      font-size: 10px;
      color: rgba(255, 255, 255, 0.75);
    }

    .progress-area {
      width: 60%;
      bottom: 24px;
      left: 24px;
      padding-top: 1rem;
      margin-top: auto;

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
        min-height: 280px;
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
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
      }
    }
  }
}

.desktop-course-card .description {
  margin-bottom: 0 !important;
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
</style>

<style>
.owl-theme .owl-dots .owl-dot.active span {
  background: #0056d2 !important;
  width: 40px;
}

.owl-theme .owl-dots .owl-dot:hover span {
  background: #0056d2 !important;
}
</style>
