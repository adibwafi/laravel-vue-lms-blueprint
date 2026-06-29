<template>
  <v-row class="navbar">
    <div class="left-title">
      <router-link v-if="link !== ''" :to="link" class="router-link">
        <div class="back-icon">
          <img src="@/assets/img/icn-back.png" alt="back icon" />
        </div>
      </router-link>
      <p class="navbar-title">{{ title }}</p>
    </div>
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
      <div class="navbar-profile" @click="toggleDropdown">
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
        <div v-if="dropdownOpen">
          <img
            src="@/assets/img/icn-dropdown-up.png"
            alt="Dropdown Icon"
            class="logo"
          />
        </div>
        <div v-else>
          <img
            src="@/assets/img/icn-dropdown.png"
            alt="Dropdown Icon"
            class="logo"
          />
        </div>
        <div v-if="dropdownOpen" class="dropdown-menu">
          <ul>
            <router-link to="/profile" class="router-link">
              <li>
                <div class="menu-item">
                  <img
                    class="icon"
                    src="@/assets/img/icn-profil-saya.png"
                    alt="icon profil"
                  />
                  <span class="menu-text">Profil Saya</span>
                </div>
              </li>
            </router-link>
            <router-link to="/my-course" class="router-link">
              <li>
                <div class="menu-item">
                  <img
                    class="icon"
                    src="@/assets/img/icn-lihat-kelas.png"
                    alt="icon lihat kelas"
                  />
                  <span class="menu-text">Lihat Kelas</span>
                </div>
              </li>
            </router-link>
            <router-link to="/profile/edit" class="router-link">
              <li>
                <div class="menu-item">
                  <img
                    class="icon"
                    src="@/assets/img/icn-pengaturan.png"
                    alt="icon pengaturan"
                  />
                  <span class="menu-text">Pengaturan</span>
                </div>
              </li>
            </router-link>
            <li @click="logout" class="logout">
              <div class="menu-item">
                <img
                  class="icon"
                  src="@/assets/img/icn-keluar.png"
                  alt="icon keluar"
                />
                <span>Keluar</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </v-row>
</template>

<script>
export default {
  name: "NavbarDesktop",
  props: {
    title: {
      type: String,
      default: "",
    },
    link: {
      type: String,
      default: "",
    },
  },
  data: () => ({
    image: "",
    dropdownOpen: false,
  }),
  methods: {
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
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    logout() {
      this.$root.token(false);
      this.$root.userId(false);
      this.$root.user(false);
      this.$router.replace("/");
    },
  },
  mounted() {
    this.getProfile();
  },
};
</script>

<style scoped>
.navbar {
  padding: 24px 56px 26px 56px;
  justify-content: space-between;
  align-items: center;
  margin: 0;
  height: auto;
  border-bottom: 1px solid var(--Black-Scale-Black-50, #ccc);
  background: white;

  .left-title {
    display: flex;
    flex-direction: row;
    align-items: center;

    .navbar-title {
      color: var(--Black-Scale-Black-700, #3d3d3d);
      font-size: 16px;
      font-weight: 600;
      line-height: 180%;
      margin-bottom: 0;
    }

    .back-icon {
      margin-right: 32px;

      img {
        height: 18px;
        margin-bottom: -4px;
      }
    }
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
      }

      .logo {
        width: 24px;
        height: 24px;
      }
    }

    .dropdown-menu {
      position: absolute;
      top: 70px;
      right: 20px;
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .dropdown-menu ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    .dropdown-menu ul li {
      padding: 12px 12px 14px 12px;
      cursor: pointer;
    }

    .dropdown-menu ul li:hover {
      background-color: #f0f0f0;
    }

    .router-link {
      text-decoration: none;
      color: inherit;
    }

    .router-link:hover {
      color: inherit;
    }

    .dropdown-menu .router-link {
      color: #3d3d3d;
    }

    .logout {
      color: #e54545;
      cursor: pointer;
      font-size: 16px;
      font-weight: 500;
      line-height: 180%;
    }

    .menu-text {
      color: var(--Black-Scale-Black-700, #3d3d3d);
      font-size: 16px;
      font-weight: 500;
      line-height: 180%;
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .icon {
      width: 24px;
      height: 24px;
    }
  }
}
</style>
