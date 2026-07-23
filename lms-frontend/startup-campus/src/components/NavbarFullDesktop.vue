<template>
  <v-row class="navbar">
    <a @click="goToHome()">
      <img src="@/assets/img/logo.png" alt="Logo" class="sidebar-logo"
    /></a>
    <div class="content">
      <p class="navbar-title">{{ title }}</p>
      <div class="navbar-icon">
        <div class="navbar-fitur">
          <img
            src="@/assets/img/icn-notifikasi.png"
            alt="Notifikasi Icon"
            class="img"
          />
          <img
            src="@/assets/img/icn-keranjang.png"
            alt="Keranjang Icon"
            class="img"
          />
        </div>
        <v-divider :thickness="2" vertical />
        <div class="navbar-profile">
          <img
            :src="image"
            alt="Foto Profil"
            v-if="image"
            class="profile-picture"
          />
          <img
            src="@/assets/img/img-placeholder.jpeg"
            alt="Foto Profil"
            v-else
            class="profile-picture"
          />
          <img
            src="@/assets/img/icn-dropdown.png"
            alt="Dropdown Icon"
            class="logo"
          />
        </div>
      </div>
    </div>
  </v-row>
</template>

<script>
export default {
  name: "NavbarFullDesktop",
  props: {
    title: {
      type: String,
      default: "",
    },
  },
  data: () => ({
    image: "",
  }),
  methods: {
    goToHome() {
      this.$router.push({
        path: `/home`,
      });
    },
    getProfile() {
      const cachedUser = this.$root.user();
      if (cachedUser && (cachedUser.fullname || cachedUser.email)) {
        this.data = cachedUser;
        if (this.data.image)
          this.image = this.$root.storageBaseURL + this.data.image;
        return;
      }
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
            this.$root.user(this.data);
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
    this.getProfile();
  },
};
</script>

<style scoped>
.navbar {
  padding: 26px 145px 30px 145px;
  justify-content: space-between;
  align-items: center;
  margin: 0;
  height: auto;
  border-bottom: 1px solid var(--Black-Scale-Black-50, #ccc);
  background: white;

  .sidebar-logo {
    display: flex;
    max-width: 136px;
    height: auto;
    padding-right: 51px;
  }

  .content {
    display: flex;
    padding-left: 5px;
    justify-content: space-between;
    align-items: center;
    flex: 1 0 0;

    .navbar-title {
      color: var(--Black-Scale-Black-700, #3d3d3d);
      font-size: 16px;
      font-weight: 600;
      line-height: 180%;
      margin-bottom: 0;
    }

    .navbar-icon {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 32px;

      .navbar-fitur {
        display: flex;
        gap: 16px;

        .img {
          width: 32px;
          height: 32px;
        }
      }

      .navbar-profile {
        display: flex;
        gap: 8px;
        align-items: center;

        .profile-picture {
          width: 48px;
          height: 48px;
          object-fit: cover;
          border-radius: 50%;
          /* border: 2px solid #fff; */
        }

        .logo {
          width: 24px;
          height: 24px;
        }
      }
    }
  }
}
</style>
