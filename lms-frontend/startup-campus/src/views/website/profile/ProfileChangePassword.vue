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
      <div class="header-title">
        <a @click="$router.go(-1)">
          <img src="@/assets/img/icn-back.png" alt="" class="icon" />
        </a>
        <div class="namepage">Ubah Sandi</div>
      </div>
      <div class="auth-area">
        <v-form class="f-form" ref="form" v-model="valid" lazy-validation>
          <div class="f-label">Sandi saat ini</div>
          <v-text-field
            v-model="oldpass"
            placeholder="Apa kata sandimu?"
            :append-icon="showPass3 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required]"
            :type="showPass3 ? 'text' : 'password'"
            @click:append="showPass3 = !showPass3"
            required
            solo
          ></v-text-field>
          <div class="f-label">Sandi Baru</div>
          <v-text-field
            v-model="nepass"
            placeholder="Ubah kata sandimu"
            :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.valid]"
            :type="showPass ? 'text' : 'password'"
            @click:append="showPass = !showPass"
            required
            solo
          ></v-text-field>
          <v-text-field
            v-model="repass"
            placeholder="Konfirmasi sandi"
            :append-icon="showPass2 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.match]"
            :type="showPass2 ? 'text' : 'password'"
            @click:append="showPass2 = !showPass2"
            required
            solo
          ></v-text-field>
          <div class="cta">
            <button :disabled="!valid" class="btn btn-blue" @click="validate">
              {{ saveLoading ? "Memuat..." : "Perbarui sandi" }}
            </button>
            <a @click="forgotPassword()" class="forgot">Lupa kata sandi?</a>
          </div>
        </v-form>
      </div>
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
import Header from "@/components/HeaderApp";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
export default {
  name: "ProfileChangePasswordPage",
  metaInfo() {
    return {
      title: "Profile Change Password - AcmeLMS",
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
      valid: false,
      oldpass: null,
      nepass: null,
      repass: null,
      showPass: false,
      showPass2: false,
      showPass3: false,
      dialogForgotDone: false,
      data: "",

      rules: {
        required: (value) => !!value || "Kata sandi wajib diisi",
        valid: (v) =>
          /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/.test(v) ||
          "Harus mengandung setidaknya satu angka dan satu huruf besar dan kecil, dan setidaknya 8 karakter atau lebih",
        match: (p) => p == this.nepass || `Kata sandi tidak sama`,
      },
      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      saveLoading: false,
    };
  },
  methods: {
    validate() {
      event.preventDefault();
      if (this.$refs.form.validate()) {
        this.saveLoading = true;
        var data = {
          oldPassword: this.oldpass,
          password: this.nepass,
          password_confirmation: this.repass,
        };
        this.$root
          .axios({
            method: "POST",
            url: `/v1/profile/update-password/${this.data.id}`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              this.alert(
                DOMPurify.sanitize("Password berhasil diubah"),
                "success"
              ); // Membersihkan nilai
              setTimeout(() => {
                this.$router.push(`/profile`);
              }, 3000);
            } else {
              this.alert(res.data.message, "danger");
            }
            this.saveLoading = false;
          })
          .catch((e) => {
            this.alert(DOMPurify.sanitize(e.message), "danger");
            this.saveLoading = false;
          });
      }
    },

    forgotPassword() {
      if (this.data.email) {
        this.isLoading = true;
        var data = {
          email: this.data.email,
          url: `${window.location}auth/verify-otp`,
          day: 2,
          type: "reset-password",
        };
        this.$root
          .axios({
            method: "POST",
            url: `/v1/auth/resend-otp`,
            data: data,
          })
          .then((res) => {
            if (res.data.success) {
              window.localStorage.setItem("emailregist", this.emailreset);
              window.localStorage.setItem("typeotp", "reset-password");
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

    getProfile() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/profile/me`,
        })
        .then((res) => {
          if (res.data.success) {
            this.data = res.data.data.me;
            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
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
    if (this.$root.token()) {
      this.getProfile();
    }
  },
};
</script>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;

  .header-title {
    display: flex;
    align-items: center;
    padding: 10px 0 24px;

    .icon {
      height: 18px;
    }

    .namepage {
      font-weight: 500;
      font-size: 14px;
      margin-left: 16px;
      padding-bottom: 4px;
    }
  }

  .img-upload {
    display: block;
    text-align: center;
    margin-bottom: 32px;

    .upload {
      .choose {
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 120px;
        height: 120px;
        border: 1px dashed $main-color;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
      }

      .choose:hover {
        border-color: #000;
        background: rgb(240, 240, 240);
      }

      .v-icon {
        color: $main-color;
      }

      .choose:hover > .v-icon {
        color: #000;
      }

      .preview {
        margin: 0 auto;
        position: relative;
        width: 120px;

        img {
          width: 120px;
        }

        .remove {
          position: absolute;
          top: 0;
          right: -10px;
          background: #fff;
          padding: 1px;
          border-radius: 50%;
          cursor: pointer;

          .v-icon {
            color: rgb(223, 54, 54);
          }
        }
      }
    }

    .upload.thumbnail {
      .choose {
        width: 70px;
        height: 70px;
        border-radius: 50%;
      }

      .preview {
        width: 72px;

        img {
          width: 72px;
          height: 72px;
          border: 2px solid #fff;
          border-radius: 50%;
          object-fit: cover;
          margin-bottom: -8px;
        }
      }
    }

    .upload.profile {
      .choose {
        width: 120px;
        height: 120px;
        border-radius: 50%;
      }

      .preview {
        img {
          width: 120px;
          height: 120px;
          border-radius: 50%;
          object-fit: cover;
        }
      }
    }

    .content {
      margin-top: 12px;

      .fullname {
        font-weight: 700;
        font-size: 16px;
      }

      .email {
        font-weight: 400;
        font-size: 13px;
        color: #bdbdbd;
      }
    }
  }

  .auth-area {
    position: relative;
    padding: 0 0px;
    width: 100%;

    .cta {
      margin: 20px 0 48px;
      text-align: center;

      .forgot {
        display: block;
        font-weight: 500;
        font-size: 12px;
        margin-top: 24px;
        text-align: center;
        color: rgba(43, 44, 39, 0.5) !important;
      }
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
