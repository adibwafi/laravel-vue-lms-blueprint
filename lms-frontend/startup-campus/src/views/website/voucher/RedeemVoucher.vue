<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div v-if="isMobile" class="main-container">
      <Header />
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <div class="header-title">
        <a @click="goToHome()">
          <div class="icon">
            <img src="@/assets/img/icn-back.png" style="" alt="" />
          </div>
        </a>
        <div class="namepage">Tukar Kode</div>
      </div>
      <div class="redeem-wrap">
        <div class="banner">
          <img src="@/assets/img/redeem.png" alt="Redeem Banner" />
        </div>

        <div class="redeem-card">
          <div class="redeem-card-header">1. Kode Voucher</div>
          <div class="redeem-card-content">
            <v-text-field
              class="redeem-input"
              placeholder="Masukan disini"
              required
              solo
              v-model="voucherToken"
            ></v-text-field>
            <button class="tukar-btn" @click="submitVoucher">Tukarkan</button>
          </div>
        </div>

        <div class="redeem-card">
          <div class="redeem-card-header">2. Kode Redeem</div>
          <div class="redeem-card-content">
            <v-text-field
              class="redeem-input"
              placeholder="Masukan disini"
              required
              solo
              v-model="redeemToken"
            ></v-text-field>
            <button class="tukar-btn" @click="submitRedeem">Tukarkan</button>
          </div>
        </div>

        <div class="paragraph">
          <span>INFO PENTING:</span>
          Pastikan sudah tukar KODE VOUCHER (dari digital platform) sebelum
          menukar KODE REDEEM (dari dashboard Prakerja). Tukar KODE REDEEM 1 jam
          sebelum memulai pelatihan.
        </div>
      </div>
    </div>
    <div v-else>
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <v-row>
        <!-- Desktop Sidebar -->
        <SidebarDesktop />
        <!-- Main Content for Desktop -->
        <v-col cols="9.5" class="main-content">
          <NavbarDesktop :title="'Redeem'" />
          <div class="content-container">
            <div class="header">
              <p class="titles">Tukar Kode</p>
            </div>

            <div class="redeem-wrap-desktop">
              <img
                class="img-desktop"
                src="@/assets/img/redeem.png"
                alt="Redeem Banner"
              />
              <div class="redeem-card">
                <div class="redeem-card-header">1. Kode Voucher</div>
                <div class="redeem-card-content">
                  <v-text-field
                    class="redeem-input"
                    placeholder="Masukan disini"
                    required
                    solo
                    v-model="voucherToken"
                  ></v-text-field>
                  <button class="tukar-btn" @click="submitVoucher">
                    Tukarkan
                  </button>
                </div>
              </div>
              <div class="redeem-card">
                <div class="redeem-card-header">2. Kode Redeem</div>
                <div class="redeem-card-content">
                  <v-text-field
                    class="redeem-input"
                    placeholder="Masukan disini"
                    required
                    solo
                    v-model="redeemToken"
                  ></v-text-field>
                  <button class="tukar-btn" @click="submitRedeem">
                    Tukarkan
                  </button>
                </div>
              </div>

              <div class="paragraph-desktop">
                <span>INFO PENTING:</span>
                Pastikan sudah tukar KODE VOUCHER (dari digital platform)
                sebelum menukar KODE REDEEM (dari dashboard Prakerja). Tukar
                KODE REDEEM 1 jam sebelum memulai pelatihan.
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </v-fade-transition>
</template>

<script>
import Header from "@/components/HeaderApp";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import SidebarDesktop from "@/components/SidebarDesktop.vue";
import NavbarDesktop from "@/components/NavbarDesktop.vue";
import { isMobile } from "@/helpers/deviceDetect";
export default {
  name: "RedeemPage",
  metaInfo() {
    return {
      title: "Redeem - AcmeLMS",
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
    SidebarDesktop,
    NavbarDesktop,
  },
  data() {
    return {
      userId: "",
      id: "",
      redeem: "",
      banner: null,
      isMobile: false,
      redeemToken: "",
      voucherToken: "",

      // notif
      isLoading: false,
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      isReady: false,
    };
  },
  mounted() {
    if (!this.$root.token()) {
      this.$root.redirectLogin();
    }
    this.isMobile = isMobile();
  },
  methods: {
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
      }, 5000);
    },
    submitRedeem() {
      if (this.redeemToken === "") {
        this.alert("Isi voucher pada form yang tersedia", "danger");
        return;
      }

      this.isLoading = true;
      const data = {
        token: this.redeemToken,
        sequence: 1,
        redirect_uri: `${process.env.VUE_APP_API_BASE_URL}/redeem/success`,
      };

      this.$root
        .axios({
          method: "post",
          url: `/v1/voucher/redeem`,
          data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            if (res.data.data.url) {
              setTimeout(() => {
                window.location.href = res.data.data.url;
              }, 2000);
            }
          } else {
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    submitVoucher() {
      if (this.voucherToken === "") {
        this.alert("Isi voucher pada form yang tersedia", "danger");
        return;
      }

      this.isLoading = true;
      const data = {
        token: this.voucherToken,
      };
      console.log("Ini data:", data);

      this.$root
        .axios({
          method: "post",
          url: `/v1/voucher/voucher`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
          } else {
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    goToHome() {
      this.$router.push({
        path: `/home`,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;
  position: relative;

  .redeem-wrap {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 32px;

    .banner {
      margin-top: 68px;
      text-align: center;

      img {
        border-radius: 20px;
        width: 168.5px;
        height: 179px;
      }
    }

    .redeem-card {
      display: flex;
      width: 370px;
      padding: 16px 16px 24px 16px;
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
      border-radius: 8px;
      border: 1px solid var(--white-grey, #e0e0e0);
      background: var(--white, #fff);
      box-shadow: 0px 16px 40px 0px rgba(112, 144, 176, 0.2);

      .redeem-card-header {
        color: #121212;
        font-family: Sora;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 180%;
      }

      .redeem-card-content {
        display: flex;
        align-items: flex-start;
        gap: 12px;

        .redeem-input {
          width: 208px;
          height: 53px;
          border-radius: 8px;
          border: 1px solid var(--white-grey, #e0e0e0);
          background: #fff;
        }
      }
    }
  }

  .header-title {
    position: relative;
    padding: 10px 0 24px;
    text-align: center;

    .icon {
      position: absolute;
      left: 0;
      top: 6px;
      background: #ffffff;
      box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
      border-radius: 10px;
      padding: 8px;

      img {
        height: 18px;
        margin-bottom: -4px;
      }
    }

    .namepage {
      white-space: nowrap;
      font-weight: 700;
      font-size: 20px;
      width: 70%;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: auto;
    }
  }
}

.main-content {
  max-width: 100%;
  padding: 0;
  gap: 24px;
  background: white;

  .content-container {
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 26px 56px 26px 56px;
    gap: 32px;

    .header {
      display: flex;
      flex-direction: column;
      gap: 26px;
      color: #1e1e1e;
      font-family: Sora;
      font-size: 40px;
      font-style: normal;
      font-weight: 600;
      line-height: 150%;
      letter-spacing: -0.6px;

      .titles {
        display: flex;
        margin: 0;
      }
    }

    .redeem-wrap-desktop {
      display: inline-flex;
      align-items: center;
      flex-direction: column;
      gap: 32px;

      .banner {
        margin-top: 68px;
        text-align: center;

        img {
          border-radius: 20px;
          width: 168.5px;
          height: 179px;
        }
      }

      .redeem-card {
        display: flex;
        width: 600px;
        padding: 16px 16px 24px 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        border-radius: 8px;
        border: 1px solid var(--white-grey, #e0e0e0);
        background: var(--white, #fff);
        box-shadow: 0px 16px 40px 0px rgba(112, 144, 176, 0.2);

        .redeem-card-header {
          color: #121212;
          font-family: Sora;
          font-size: 16px;
          font-style: normal;
          font-weight: 600;
          line-height: 180%;
        }

        .redeem-card-content {
          display: flex;
          align-items: flex-start;
          gap: 12px;

          .redeem-input {
            width: 440px;
            height: 53px;
            border-radius: 8px;
            border: 1px solid var(--white-grey, #e0e0e0);
            background: #fff;
          }
        }
      }
    }
  }
}

.paragraph {
  font-size: 14px;
  text-align: center;
  line-height: 18px;
  color: rgba(43, 44, 39, 0.6);
  font-weight: 400;
  line-height: 24px;
  align-self: stretch;

  span {
    color: #2b2c27;
    font-weight: 600;
  }
}

.paragraph-desktop {
  max-width: 600px;
  font-size: 14px;
  text-align: center;
  line-height: 18px;
  color: rgba(43, 44, 39, 0.6);
  font-weight: 400;
  line-height: 24px;
  align-self: stretch;

  span {
    color: #2b2c27;
    font-weight: 600;
  }
}

.img-desktop {
  border-radius: 20px;
  width: 168.5px;
  height: 179px;
}

.tukar-btn {
  display: flex;
  padding: 0px 24px;
  justify-content: center;
  align-items: center;
  gap: 12px;
  height: 53px;
  border-radius: 7px;
  background: var(--primary-scale-blue-500, #0056d2);
  color: var(--black-scale-white, #fff);
  font-family: "Sora";
  font-size: 14px;
  font-style: normal;
  font-weight: 600;
  line-height: 56px;
  box-shadow: 0px 0px 34px 0px rgba(18, 18, 18, 0.12);
}
</style>
