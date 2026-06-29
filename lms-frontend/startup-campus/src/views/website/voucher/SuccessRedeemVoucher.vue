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

      <div class="success-redeem-wrap">
        <div class="image-text-wrap">
          <div class="banner">
            <img
              src="@/assets/img/success-redeem.png"
              alt="Success-Redeem-Banner"
            />
          </div>
          <div class="text-wrap">
            <div class="success-header">Penukaran Berhasil</div>
            <div class="success-desc">
              Kamu telah terverifikasi, selamat mengikuti pelatihan!
            </div>
          </div>
        </div>
        <div class="button-wrap">
          <button class="kembali-btn" @click="goBack()">Kembali</button>
          <button class="mulai-btn" @click="goToHome()">Mulai Pelatihan</button>
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
  name: "SuccessRedeemVoucher",
  metaInfo() {
    return {
      title: "Success Redeem - AcmeLMS",
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
      userId: "",
      id: "",
      redeem: "",
      courses: [],
      lessons: [],
      exams: [],
      banner: null,
      isSubscribe: false,
      nextLesson: "#",
      timeExam: "",
      startExam: false,
      examDone: false,
      redeemTokentoken: "",
      voucherToken: "",

      // idExam: "",

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
    };
  },
  mounted() {
    if (!this.$root.token()) {
      this.$root.redirectLogin();
    }
  },
  methods: {
    goToHome() {
      this.$router.push({
        path: `/home?type=prakerja`,
      });
    },
    goBack() {
      this.$router.push({
        path: `/redeem`,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.success-redeem-wrap {
  display: flex;
  flex-direction: column;
  padding: 240px 4px;
  align-items: center;
  gap: 48px;
}

.banner {
  text-align: center;
  gap: 32px;

  img {
    width: 130px;
    height: 130px;
  }
}

.image-text-wrap {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.button-wrap {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.success-header {
  color: #2b2c27;
  text-align: center;
  font-family: Sora;
  font-size: 24px;
  font-style: normal;
  font-weight: 700;
  line-height: normal;
}

.success-desc {
  color: #666;
  text-align: center;
  font-family: Sora;
  font-size: 14px;
  font-style: normal;
  font-weight: 400;
  line-height: 24px;
  /* 171.429% */
}

.kembali-btn {
  background: var(--blue, #0056d2);
  color: var(--black-scale-white, #fff);
  border-radius: 5px;
  width: 174px;
  height: 53px;
  justify-content: center;
  align-items: center;
  font-family: "Sora";
  font-size: 14px;
  font-style: normal;
  font-weight: 600;
  line-height: 44px;
  padding: 4px 20px;
  display: flex;
  flex-direction: column;

  /* Shadow 1 */
  box-shadow: 0px 0px 34px 0px rgba(18, 18, 18, 0.12);
}

.mulai-btn {
  color: var(--blue, #0056d2);
  border: 1.5px solid var(--blue, #0056d2);
  border-radius: 5px;
  width: 174px;
  height: 53px;
  justify-content: center;
  align-items: center;
  font-family: "Sora";
  font-size: 14px;
  font-style: normal;
  font-weight: 600;
  line-height: 44px;
  padding: 4px 20px;
  display: flex;
  flex-direction: column;

  /* Shadow 1 */
  box-shadow: 0px 0px 34px 0px rgba(18, 18, 18, 0.12);
}

.text-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
</style>
