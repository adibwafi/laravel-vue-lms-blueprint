<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <img src="@/assets/img/ellipse-auth.png" alt="/" class="bg-ellipse" />
      <div class="auth-area">
        <div class="heading">Verifikasi</div>
        <div class="desc">
          Cek e-mailmu untuk mengetahui 6 kode digit. Masukkan kode secara
          tepat.
        </div>
        <v-otp-input v-model="otp" :length="length"></v-otp-input>
        <div class="cta">
          <button
            :disabled="!isActive"
            class="btn btn-blue btn-block"
            @click="verify"
          >
            Lanjut
          </button>
        </div>
        <div class="signup-txt">
          Belum dapat kode?
          <a @click="resend"> Kirim ulang</a>
        </div>
      </div>
    </div>
    <!-- Or, <a>click here to verify your account</a>, -->
  </v-fade-transition>
</template>

<script>
import Loading from "@/components/Loading";
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
  components: {
    Loading,
    Alert,
  },
  data: () => ({
    otp: "",
    length: 6,
    email: "",
    type: "",
    token: "",
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
              this.$root.token(res.data.data.token);
              this.$root.userId(res.data.data.user.id);
              this.$root.user(res.data.data.user);
              window.localStorage.removeItem("emailregist");
              window.localStorage.removeItem("typeotp");
              setTimeout(() => {
                this.$router.push(`/home`);
              }, 3000);
            }
          } else {
            this.alert(res.data.message, "danger");
          }
          this.isLoading = false;
        })
        .catch((e) => {
          this.alert(e.message, "danger");
          this.isLoading = false;
        });
    },
    resend() {
      this.isLoading = true;
      var data = {};
      if (this.type == "reset-password") {
        data = {
          email: this.email,
          url: `${window.location}auth/verify-otp`,
          type: this.type,
          day: 2,
        };
      } else {
        data = {
          email: this.email,
          url: `${window.location}auth/verify-otp`,
          type: this.type,
          minute: 30,
        };
      }
      this.$root
        .axios({
          method: "POST",
          url: `/v1/auth/resend-otp`,
          data: data,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            setTimeout(() => {
              this.$router.go(0);
            }, 3000);
          } else {
            this.alert(res.data.message, "danger");
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
    if (localStorage.typeotp) this.type = localStorage.typeotp;
    if (!localStorage.emailregist && !localStorage.typeotp) {
      this.$router.push(`/`);
    }
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

      span {
        color: #0056d2;
        font-weight: 700;
      }
    }

    .cta {
      margin: 70px 0 48px;
    }

    .signup-txt {
      position: absolute;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      text-align: center;
      font-weight: 500;
      font-size: 12px;
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
