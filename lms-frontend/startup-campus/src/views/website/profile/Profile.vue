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
      <div class="header-home">
        <img src="@/assets/img/icn-logo.png" alt="" class="brand" />
        <router-link to="/home">
          <img src="@/assets/img/icn-notif.png" alt="" class="notif" />
        </router-link>
      </div>
      <div class="heading">
        <h4>Profil</h4>
      </div>
      <div class="profile-box">
        <div class="image">
          <img :src="image" alt="" v-if="image" />
          <img src="@/assets/img/img-placeholder.jpeg" alt="" v-else />
        </div>

        <div class="detail">
          <div class="name">{{ data.fullname }}</div>
          <div class="email">{{ data.email }}</div>
        </div>
      </div>
      <div class="profile-menu-area">
        <h6>Your account</h6>
        <div class="menu-box">
          <router-link to="/profile/edit" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-user.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Pengaturan Akun</div>
                <div class="desc">Lihat informasi tentang akun</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </router-link>
          <router-link to="/achievement" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-user.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Pencapaian</div>
                <div class="desc">Make changes to your account</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </router-link>
          <router-link to="/profile/change-password" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-lock.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Ubah Kata Sandi</div>
                <div class="desc">Ubah sandi kapan pun</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </router-link>
        </div>
      </div>
      <div class="profile-menu-area">
        <h6>Lanjutan</h6>
        <div class="menu-box">
          <router-link to="/" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-user.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Pusat Bantuan</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </router-link>
          <router-link to="/" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-user.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Tentang Aplikasi</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </router-link>
          <a @click="logout" class="menu-list">
            <div class="m-left">
              <div class="image">
                <img src="@/assets/img/icn-user.png" alt="" />
              </div>
              <div class="det">
                <div class="name">Keluar</div>
              </div>
            </div>
            <div class="m-right">
              <img src="@/assets/img/icn-arrow-right.png" alt="" />
            </div>
          </a>
        </div>
      </div>
      <div class="main-menu">
        <div class="menu-area">
          <router-link to="/home" class="list">
            <img src="@/assets/img/icn-home.png" alt="" />
            <div class="name">Home</div>
          </router-link>
          <router-link to="/my-course" class="list">
            <img src="@/assets/img/icn-course.png" alt="" />
            <div class="name">My Course</div>
          </router-link>
          <router-link to="/profile" class="list active">
            <img src="@/assets/img/icn-profile-ac.png" alt="" />
            <div class="name">Profile</div>
          </router-link>
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
  name: "ProfilePage",
  metaInfo() {
    return {
      title: "Profile - AcmeLMS",
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
      data: "",
      image: "",
      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
    };
  },
  methods: {
    logout() {
      this.$root.token(false);
      this.$root.userId(false);
      this.$root.user(false);
      this.$router.replace("/");
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
            this.data = res.data.data.me;
            if (this.data.image)
              this.image = this.$root.storageBaseURL + this.data.image;
            this.isLoading = false;
            return;
          } else {
            this.isLoading = true;
            this.alert(res.data.message, "danger");
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
  },
  mounted() {
    if (this.$root.token()) {
      this.getProfile();
    } else {
      this.$root.redirectLogin();
    }
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  background: #f6f8fd;
  padding-bottom: 4rem;

  .heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;

    h4 {
      font-size: 20px;
    }

    a {
      font-size: 12px;
    }
  }

  .profile-box {
    display: flex;
    align-items: center;
    background: #0056d2;
    box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
    border-radius: 10px;
    padding: 20px 26px;
    margin-bottom: 24px;

    .image {
      img {
        width: 57px;
        height: 57px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
      }
    }

    .detail {
      margin-left: 16px;
      padding-bottom: 4px;

      .name {
        font-weight: 700;
        font-size: 16px;
        color: #fff;
      }

      .email {
        font-size: 12px;
        color: #d7d7d7;
      }
    }
  }

  .profile-menu-area {
    margin-bottom: 40px;

    h6 {
      font-weight: 500;
      font-size: 14px;
      margin-bottom: 16px;
    }

    .menu-box {
      background: #ffffff;
      border-radius: 10px;
      padding: 24px;

      .menu-list:last-child {
        margin-bottom: 0;
      }

      .menu-list {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;

        .m-left {
          display: flex;
          align-items: center;

          .image {
            width: 40px;

            img {
              width: 40px;
            }
          }

          .det {
            margin-left: 16px;
            padding-bottom: 4px;

            .name {
              font-weight: 500;
              font-size: 13px;
              margin-bottom: 0px;
              color: #181d27;
            }

            .desc {
              font-size: 10px;
              color: #ababab;
            }
          }
        }

        .m-right {
          img {
            width: 8px;
          }
        }
      }
    }
  }
}

.header-home {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0 24px;

  .brand {
    width: 30px;
  }

  .notif {
    width: 30px;
  }
}
</style>
