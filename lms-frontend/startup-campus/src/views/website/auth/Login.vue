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
        <img src="@/assets/img/login_img.png" alt="" />
        <div class="heading">Mulai Belajar</div>
        <v-form class="f-form">
          <div class="f-label">E-mail</div>
          <v-text-field
            outlined
            dense
            placeholder="Ketik e-mailmu"
            prepend-inner-icon="mdi-email-outline"
            v-model="email"
          ></v-text-field>
          <div class="f-label">Kata Sandi</div>
          <v-text-field
            outlined
            dense
            placeholder="Ketik kata sandi"
            prepend-inner-icon="mdi-lock-outline"
            :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPass ? 'text' : 'password'"
            @click:append="showPass = !showPass"
            v-model="password"
          ></v-text-field>
          <div class="text-help">
            <v-checkbox v-model="checkbox" label="Ingatkan saya"></v-checkbox>
            <a @click.stop="dialogForgot = true">Lupa sandi?</a>
          </div>
          <div class="cta">
            <a class="btn btn-blue btn-block" @click="Login">Masuk</a>
          </div>
        </v-form>
        <div class="signup-txt mb-2">
          Ingin melihat course tanpa login?
          <router-link to="/home">Klik disini</router-link>
        </div>
        <div class="signup-txt">
          Belum punya akun?
          <router-link to="/auth/register">Daftar sekarang</router-link>
        </div>
      </div>
      <!-- Dialog Forgot Password -->
      <v-dialog v-model="dialogForgot" max-width="350">
        <v-card class="v-modal">
          <div class="dialog-lms">
            <div class="heading">Lupa kata sandi?</div>
            <div class="desc">
              Masukkan e-mail agar kami kirim link untuk ubah sandimu.
            </div>
            <v-form class="f-form">
              <div class="f-label">E-mail</div>
              <v-text-field
                outlined
                dense
                placeholder="Ketik e-mailmu di sini"
                prepend-inner-icon="mdi-email-outline"
                v-model="emailreset"
                :rules="emailRules"
              ></v-text-field>
            </v-form>
            <div class="cta">
              <a
                class="btn btn-blue btn-block mb"
                @click="resetPassword()"
                :disabled="isLoading"
                >{{ isLoading ? "Memuat..." : "Ubah sandi" }}</a
              >
              <a class="btn btn-text tf btn-block" @click="dialogForgot = false"
                >Batalkan</a
              >
            </div>
          </div>
        </v-card>
      </v-dialog>
      <!-- Dialog Forgot Password Done -->
      <v-dialog v-model="dialogForgotDone" max-width="350">
        <v-card class="v-modal">
          <div class="dialog-lms">
            <div class="heading">Cek e-mailmu</div>
            <div class="desc">Cek e-mail dan ikuti langkah selanjutnya ya</div>
            <div class="cta">
              <a
                class="btn btn-blue btn-block"
                @click="dialogForgotDone = false"
                >Oke</a
              >
            </div>
          </div>
        </v-card>
      </v-dialog>
    </div>
  </v-fade-transition>
</template>

<script>
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
// import axios from "axios";
export default {
  name: "LoginPage",
  metaInfo() {
    return {
      title: "Login - AcmeLMS",
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
    isLoading: false,
    dialogForgot: false,
    dialogForgotDone: false,
    showPass: false,
    checkbox: false,
    email: "",
    password: "",
    emailreset: "",

    emailRules: [
      (v) => !!v || "E-mail is required",
      (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
    ],

    // notif
    notifsuccess: false,
    notifdanger: false,
    textNotif: "",
  }),
  methods: {
    resetPassword() {
      if (this.emailreset) {
        this.isLoading = true;
        var data = {
          email: DOMPurify.sanitize(this.emailreset),
          url: `${window.location}auth/verify-otp`,
          day: 2,
          type: "reset-password",
        };
        // console.log(data);
        // axios.get("/sanctum/csrf-cookie").then((res) => {
        //   console.log(res);
        // });
        this.$root
          .axios({
            method: "POST",
            url: `/v1/auth/resend-otp`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              window.localStorage.setItem("emailregist", this.emailreset);
              window.localStorage.setItem("typeotp", "reset-password");
              this.dialogForgot = false;
              this.dialogForgotDone = true;
              setTimeout(() => {
                this.$router.push(`/auth/verification`);
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
      } else {
        this.alert("Email is required", "danger");
      }
    },
    Login() {
      this.isLoading = true;
      var data = {
        email: DOMPurify.sanitize(this.email),
        password: DOMPurify.sanitize(this.password),
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/auth/login`,
          data: data,
        })
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.$root.token(res.data.data.token);
            this.$root.userId(res.data.data.user.id);
            this.$root.user(res.data.data.user);
            const redirect = this.$route.query.redirect;
            this.alert(res.data.message, "success");
            if (res.data.data.user.email_verified_at) {
              setTimeout(() => {
                if (redirect) {
                  this.$router.push(redirect);
                } else {
                  this.$router.push(`/home`);
                }
              }, 500);
            } else {
              window.localStorage.setItem(
                "emailregist",
                res.data.data.user.email
              );
              window.localStorage.setItem("typeotp", "verify-email");
              setTimeout(() => {
                this.$router.push(`/auth/verification`);
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
    const redirect = this.$route.query.redirect;
    if (redirect) {
      this.alert("Kamu perlu login untuk mengakses halaman ini", "danger");
    }

    var token = this.$root.token();
    if (token) {
      this.$router.replace("/home");
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
    // position: absolute;
    // bottom: 50px;
    // left: 0;
    // padding: 0 20px;
    padding: 40px 20px 40px;
    width: 100%;

    .heading {
      font-weight: 700;
      font-size: 20px;
      text-align: center;
      margin-bottom: 40px;
    }

    .text-help {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: -30px;

      a {
        font-weight: 600;
        font-size: 13px;
      }
    }

    .cta {
      margin: 20px 0 48px;
    }

    .signup-txt {
      text-align: center;
      font-weight: 500;
      font-size: 13px;
    }
  }
}

.dialog-lms {
  .f-form {
    margin-bottom: 16px;
  }
}
</style>

<style lang="scss">
.text-help {
  .v-label {
    font-size: 13px !important;
    color: #2b2c27;
  }
}
</style>
