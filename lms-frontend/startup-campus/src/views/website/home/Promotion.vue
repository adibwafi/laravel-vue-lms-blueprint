<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <Header />
      <div class="header-title">
        <a @click="$router.go(-1)">
          <img src="@/assets/img/icn-back.png" alt="" class="icon" />
        </a>
        <div class="namepage">Promosi</div>
      </div>
      <div v-if="loadScreen">
        <v-skeleton-loader
          type="image"
          v-for="i in 6"
          :key="i"
          class="mb-4"
        ></v-skeleton-loader>
      </div>
      <div class="promo-banner" v-else>
        <a
          :href="i.url"
          target="_blank"
          rel="noopener noreferrer"
          v-for="(i, idx) in banners"
          :key="idx"
        >
          <img :src="`${storageBaseUrl}${i.image}`" :alt="i.name" />
        </a>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
export default {
  name: "PromoPage",
  metaInfo() {
    return {
      title: "Promo - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: { Header },
  data: () => ({
    storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
    loadScreen: false,
    banners: [],
  }),
  methods: {
    getBanner() {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/banner-promotion`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
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
  },
  mounted() {
    this.getBanner();
  },
  computed: {},
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;

  .promo-banner {
    img {
      margin-bottom: 24px;
      border-radius: 25px;
    }
  }
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
