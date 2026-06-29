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
      <div class="back">
        <router-link to="/"
          ><img src="@/assets/img/icn-back.png" alt="" />
        </router-link>
      </div>
      <div class="auth-area">
        <div class="heading">Buat Akun</div>
        <v-form class="f-form" ref="form" v-model="valid" lazy-validation>
          <div class="f-label">Nama lengkap</div>
          <v-text-field
            outlined
            dense
            placeholder="Ketik nama lengkapmu"
            :rules="[(v) => !!v || 'Nama lengkap wajib diisi']"
            v-model="fullname"
          ></v-text-field>
          <div class="f-label">Nomor handphone</div>
          <v-text-field
            outlined
            dense
            placeholder="Ketik nomor HP-mu yang aktif"
            prepend-inner-icon="mdi-phone-outline"
            v-model="phone"
            :rules="phoneRules"
            type="number"
          ></v-text-field>
          <div class="f-label">E-mail</div>
          <v-text-field
            outlined
            dense
            placeholder="Ketik e-mailmu"
            prepend-inner-icon="mdi-email-outline"
            :rules="emailRules"
            v-model="email"
          ></v-text-field>
          <div class="f-label">Password</div>
          <v-text-field
            outlined
            dense
            placeholder="Buat sandi (minimal 8 karakter)"
            prepend-inner-icon="mdi-lock-outline"
            :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPass ? 'text' : 'password'"
            @click:append="showPass = !showPass"
            :rules="passRules"
            v-model="password"
          ></v-text-field>
          <div class="signup-txt">
            Saya menyetujui
            <router-link to="/auth/privacy-policy"
              >Syarat dan Ketentuan serta Kebijakan Privasi.</router-link
            >
          </div>
          <div class="cta">
            <button
              class="btn btn-blue btn-block"
              :disabled="!valid"
              @click="validate"
            >
              Daftar
            </button>
          </div>
        </v-form>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
export default {
  name: "RegisterPage",
  metaInfo() {
    return {
      title: "Register - AcmeLMS",
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
  data() {
    return {
      isLoading: false,
      showPass: false,
      checkbox: false,
      valid: false,
      fullname: "",
      email: "",
      phone: "",
      password: "",
      emailRules: [
        (v) => !!v || "E-mail wajib diisi",
        (v) => /.+@.+\..+/.test(v) || "E-mail harus valid",
      ],
      phoneRules: [
        (v) => !!v || "Nomor HP wajib diisi",
        (v) => v.length >= 10 || "Min 10 numbers",
      ],
      passRules: [
        (v) => !!v || "Kata sandi wajib diisi",
        (v) =>
          /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&()_+\-=[\]{};':"\\|,.<>/?`~*]{8,}$/.test(
            v
          ) ||
          "Harus mengandung setidaknya satu angka dan satu huruf besar dan kecil, dan setidaknya 8 karakter atau lebih",
      ],

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
    };
  },
  methods: {
    validate() {
      event.preventDefault();
      if (this.$refs.form.validate()) {
        this.isLoading = true;
        // Gunakan DOMPurify untuk membersihkan input dari pengguna
        var data = {
          fullname: DOMPurify.sanitize(this.fullname),
          phone: DOMPurify.sanitize(this.phone),
          email: DOMPurify.sanitize(this.email),
          password: DOMPurify.sanitize(this.password),
          url: `${window.location.origin}/auth/verify-otp`,
        };
        this.$root
          .axios({
            method: "post",
            url: `/v1/auth/register`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              window.localStorage.setItem("emailregist", res.data.data.email);
              window.localStorage.setItem("typeotp", "verify-email");
              this.alert(res.data.message, "success");
              setTimeout(() => {
                this.$router.push(`/auth/verification`);
              }, 3000);
            } else {
              // console.log(res.data.message);
              if (res.data.code == 1) {
                this.alert(res.data.data.email[0], "danger");
              }
            }
            this.isLoading = false;
          })
          .catch((e) => {
            this.alert(e.message, "danger");
            this.isLoading = false;
          });
      }
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

  .back {
    position: absolute;
    top: 30px;
    left: 20px;

    img {
      width: 32px;
    }
  }

  .auth-area {
    // position: absolute;
    // bottom: 50px;
    // left: 0;
    // padding: 0 20px;
    padding: 100px 20px 40px;
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
        font-size: 12px;
      }
    }

    .cta {
      margin: 20px 0 48px;
    }

    .signup-txt {
      text-align: center;
      font-weight: 500;
      font-size: 12px;
      padding: 16px 0;
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
