<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <img src="@/assets/img/ellipse-auth.png" alt="/" class="bg-ellipse" />
      <div class="auth-area">
        <!-- <div class="desc">Please wait a moment, you will be redirected</div> -->
        <div class="desc">Harap tunggu sebentar, kamu akan dialihkan</div>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Alert from "@/components/Alert.vue";
export default {
  name: "VerifyPage",
  metaInfo() {
    return {
      title: "Verify - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: { Alert },
  data: () => ({
    otp: "",
    length: 6,
    email: "",
    type: "",
    // notif
    isLoading: false,
    notifsuccess: false,
    notifdanger: false,
    textNotif: "",
  }),
  methods: {
    verify() {
      this.isLoading = true;
      var data = {
        email: this.email,
        otp: this.otp,
        type: this.type,
      };
      this.$root
        .axios({
          method: "POST",
          url: `/v1/auth/verification-otp`,
          data: data,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            if (this.type == "reset-password") {
              window.localStorage.setItem("myotp", this.otp);
              setTimeout(() => {
                this.$router.push(`/auth/reset-password`);
              }, 3000);
            } else {
              window.localStorage.removeItem("emailregist");
              window.localStorage.removeItem("typeotp");
              this.$root.token(res.data.data.token);
              this.$root.userId(res.data.data.user.id);
              this.$root.user(res.data.data.user);
              setTimeout(() => {
                this.$router.push(`/`);
              }, 3000);
            }
          } else {
            this.alert(res.data.message, "danger");
            setTimeout(() => {
              this.$router.push(`/`);
            }, 3000);
          }
          this.isLoading = false;
        })
        .catch((e) => {
          this.alert(e.message, "danger");
          this.isLoading = false;
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
  computed: {
    isActive() {
      return this.otp.length === this.length;
    },
  },
  mounted() {
    if (localStorage.emailregist) this.email = localStorage.emailregist;
    this.type = this.$route.params.type;
    this.otp = this.$route.params.otp;
    setTimeout(() => {
      this.verify();
    }, 2000);
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  .bg-ellipse {
    position: absolute;
    top: 0;
    left: 0;
    width: 65%;
  }

  .auth-area {
    padding: 0 20px;
    width: 100%;
    padding-top: 5.5rem;

    .heading {
      font-weight: 700;
      font-size: 28px;
      margin-bottom: 24px;
    }

    .desc {
      font-weight: 500;
      font-size: 14px;
      margin-bottom: 56px;
      text-align: center;
    }
  }
}
</style>

<style lang="scss">
.text-help {
  .v-label {
    font-size: 12px !important;
    color: #2b2c27;
  }
}
</style>
