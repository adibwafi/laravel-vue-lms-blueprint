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
        <div class="heading">Log in to Dashboard</div>
        <v-form class="f-form">
          <div class="f-label">Email</div>
          <v-text-field
            outlined
            dense
            placeholder="Enter your email"
            prepend-inner-icon="mdi-email-outline"
            v-model="email"
          ></v-text-field>
          <div class="f-label">Password</div>
          <v-text-field
            v-model="password"
            outlined
            dense
            placeholder="Enter a password"
            prepend-inner-icon="mdi-lock-outline"
            :append-icon="showPass ? 'mdi-eye' : 'mdi-eye-off'"
            :type="showPass ? 'text' : 'password'"
            @click:append="showPass = !showPass"
          ></v-text-field>
          <div class="cta">
            <a class="btn btn-blue btn-block" @click="Login()">Log in</a>
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
  name: "LoginAdmin",
  metaInfo() {
    return {
      title: "Login Admin - AcmeLMS",
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
    showPass: false,
    checkbox: false,
    email: "",
    password: "",

    // notif
    notifsuccess: false,
    notifdanger: false,
    textNotif: "",
    isLoading: false,
  }),
  methods: {
    Login() {
      this.isLoading = true;
      var data = {
        email: DOMPurify.sanitize(this.email), // Membersihkan nilai
        password: DOMPurify.sanitize(this.password), // Membersihkan nilai
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
            // console.log(res.data.data.user.roles[0].name == "Super-Admin");
            if (res.data.data.user.roles.length > 0) {
              this.$root.token(res.data.data.token);
              this.alert(DOMPurify.sanitize(res.data.message), "success"); // Membersihkan nilai
              setTimeout(() => {
                this.$router.push(`/admin/welcome`);
              }, 3000);
            } else {
              this.alert("Akses di tolak!", "danger");
            }
          } else {
            this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
          }
          this.isLoading = false;
        })
        .catch((e) => {
          this.alert(DOMPurify.sanitize(e.message), "danger"); // Membersihkan nilai
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
    var token = this.$root.token();
    if (token) {
      this.$router.replace("/admin/welcome");
    }
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #fafafa;

  .bg-ellipse {
    position: absolute;
    top: 0;
    left: 0;
    width: 65%;
  }

  .auth-area {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    left: 0;
    padding: 0 20px;
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
