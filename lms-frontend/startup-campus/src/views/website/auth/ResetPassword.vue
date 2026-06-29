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
      <div class="auth-area">
        <div class="heading">Ubah sandi</div>
        <v-form class="f-form" ref="form" v-model="valid" lazy-validation>
          <div class="f-label">Kata sandi baru</div>
          <v-text-field
            v-model="nepass"
            placeholder="Ketik kata sandi baru"
            :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.valid]"
            :type="showPass ? 'text' : 'password'"
            @click:append="showPass = !showPass"
            required
            solo
          ></v-text-field>
          <div class="f-label">Konfirmasi kata sandi</div>
          <v-text-field
            v-model="repass"
            placeholder="Konfirmasi kata sandi"
            :append-icon="showPass2 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.match]"
            :type="showPass2 ? 'text' : 'password'"
            @click:append="showPass2 = !showPass2"
            required
            solo
          ></v-text-field>
          <div class="cta">
            <button :disabled="!valid" class="btn btn-blue" @click="validate">
              Simpan dan masuk
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
export default {
  name: "ResetPasswordPage",
  metaInfo() {
    return {
      title: "Reset Password - AcmeLMS",
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
      valid: false,
      showPass: false,
      showPass2: false,
      nepass: null,
      repass: null,
      checkbox: false,
      email: "",
      type: "",
      otp: "",

      rules: {
        required: (value) => !!value || "Kata sandi wajib diisi",
        valid: (v) =>
          /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&()_+\-=[\]{};':"\\|,.<>/?`~*]{8,}$/.test(
            v
          ) ||
          "Harus mengandung setidaknya satu angka dan satu huruf besar dan kecil, dan setidaknya 8 karakter atau lebih",
        match: (p) => p == this.nepass || `Kata sandi tidak sama`,
      },
      // notif
      isLoading: false,
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
        var data = {
          email: this.email,
          otp: this.otp,
          password: this.nepass,
          password_confirmation: this.repass,
        };
        this.$root
          .axios({
            method: "POST",
            url: `/v1/auth/reset-password`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              window.localStorage.removeItem("emailregist");
              window.localStorage.removeItem("typeotp");
              window.localStorage.removeItem("myotp");
              this.alert(res.data.message, "success");
              setTimeout(() => {
                this.$router.push(`/`);
              }, 3000);
            } else {
              if (res.data.code == 1) {
                this.alert(res.data.data.password[0], "danger");
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
  mounted() {
    this.valid = false;
    if (localStorage.emailregist) this.email = localStorage.emailregist;
    if (localStorage.typeotp) this.type = localStorage.typeotp;
    if (localStorage.myotp) this.otp = localStorage.myotp;
    if (
      !localStorage.emailregist &&
      !localStorage.typeotp &&
      !localStorage.myotp
    ) {
      this.$router.push(`/`);
    }
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;

  .auth-area {
    position: relative;
    padding: 0 20px;
    padding-top: 3rem;
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
      text-align: center;
    }

    .signup-txt {
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

/* Change the white to any color */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px white inset !important;
}
</style>
