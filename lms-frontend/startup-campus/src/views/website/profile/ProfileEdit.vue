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
        <div class="namepage">My Account</div>
      </div>
      <div class="auth-area">
        <v-form class="f-form" ref="form" v-model="valid" lazy-validation>
          <div class="img-upload">
            <input
              type="file"
              @change="onFileSelected"
              ref="imgUpload"
              hidden
            />
            <div class="upload thumbnail">
              <div class="preview" v-if="img_d">
                <img :src="img_d" alt="" class="preview" />
                <div class="remove" @click="removeimg()">
                  <v-icon>mdi-pencil-circle</v-icon>
                </div>
              </div>
              <div class="choose" v-on:click="$refs.imgUpload.click()" v-else>
                <v-icon>mdi-account-circle</v-icon>
              </div>
            </div>
            <div class="content">
              <div class="fullname">{{ data.fullname }}</div>
              <div class="email">{{ data.email }}</div>
            </div>
          </div>
          <v-text-field
            v-model="fullname"
            placeholder="Ketik nama lengkapmu"
            :rules="[(v) => !!v || 'Nama lengkap wajib diisi']"
            required
            solo
          ></v-text-field>
          <v-text-field
            v-model="phone"
            placeholder="Masukkan nomor HP aktif"
            :rules="phoneRules"
            type="number"
            required
            solo
          ></v-text-field>
          <div class="cta">
            <button :disabled="!valid" class="btn btn-blue" @click="validate">
              {{ saveLoading ? "Memuat..." : "Perbarui Profil" }}
            </button>
          </div>
        </v-form>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/LoadingFull";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
export default {
  name: "ProfileEditPage",
  metaInfo() {
    return {
      title: "Edit Profile - AcmeLMS",
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
      fullname: "",
      phone: "",
      image: null,
      img_d: "",
      data: "",

      phoneRules: [
        (v) => !!v || "Phone wajib diisi",
        (v) => v.length >= 10 || "Minimal 10 angka",
      ],
      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      saveLoading: false,
    };
  },
  methods: {
    onFileSelected(event) {
      this.image = event.target.files[0];
      this.img_d = URL.createObjectURL(this.image);
    },
    removeimg() {
      this.image = "";
      this.img_d = "";
    },
    validate() {
      event.preventDefault();
      if (this.$refs.form.validate()) {
        this.saveLoading = true;
        var data = new FormData();
        data.append("fullname", this.fullname);
        data.append("phone", this.phone);
        data.append("image", this.image === null ? "" : this.image);
        // data.append("image", this.image);
        this.$root
          .axios({
            method: "POST",
            url: `/v1/profile/update/${this.data.id}`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              this.alert("Data berhasil diubah", "success");
              this.getProfile();
            } else {
              this.alert(res.data.message, "danger");
            }
            this.saveLoading = false;
          })
          .catch((e) => {
            this.alert(e.message, "danger");
            this.saveLoading = false;
          });
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
          // console.log(res);
          if (res.data.success) {
            var data = res.data.data.me;
            this.data = data;
            this.fullname = DOMPurify.sanitize(data.fullname); // Membersihkan nilai
            if (data.phone) this.phone = DOMPurify.sanitize(data.phone); // Membersihkan nilai
            if (data.image) {
              this.img_d = this.$root.storageBaseURL + data.image;
            }
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
