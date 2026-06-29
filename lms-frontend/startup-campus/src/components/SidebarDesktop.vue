<template>
  <v-col cols="2.5" class="sidebar">
    <a @click="goToHome()">
      <img src="@/assets/img/logo.png" alt="Logo" class="sidebar-logo"
    /></a>
    <div class="sidebar-organism">
      <div class="sidebar-molecule">
        <p class="sidebar-title">Course Management</p>
        <div class="sidebar-content">
          <SidebarItem
            icon="icn-dashboard.png"
            iconHighlighted="icn-dashboard-white.png"
            label="Dashboard"
            to="/"
            :highlighted="$route.path === '/home'"
          />
          <SidebarItem
            icon="icn-kelas.png"
            iconHighlighted="icn-kelas-white.png"
            label="Kelas"
            to="/my-course"
            :highlighted="
              $route.path.startsWith('/course') ||
              $route.path.startsWith('/topic') ||
              $route.path.startsWith('/my-course')
            "
          />
          <!-- <SidebarItem
            icon="icn-sertifikat.png"
            iconHighlighted="icn-sertifikat-white.png"
            label="Sertifikat"
            to="/"
          />
          <SidebarItem
            icon="icn-event.png"
            iconHighlighted="icn-event-white.png"
            label="Event"
            to="/"
          /> -->
          <SidebarItem
            icon="icn-sertifikat.png"
            iconHighlighted="icn-event-white.png"
            label="Alumni Lounge"
            to="#"
          />
        </div>
      </div>
      <div class="sidebar-molecule">
        <p class="sidebar-title">Finance</p>
        <div class="sidebar-content">
          <SidebarItem
            icon="icn-redeem.png"
            iconHighlighted="icn-redeem-white.png"
            label="Redeem"
            to="/redeem"
            :highlighted="$route.path === '/redeem'"
          />
          <!-- <SidebarItem
            icon="icn-payment.png"
            iconHighlighted="icn-payment-white.png"
            label="Payment"
            to="/"
          /> -->
        </div>
      </div>
    </div>
    <div class="alumni-card">
      <img
        src="@/assets/img/bg-sertifikat.png"
        alt="Background Sertifikat"
        class="img"
      />
      <img src="@/assets/img/bg-blur.png" alt="Background Blur" class="blur" />
      <a href="#">
        <v-btn class="button-alumni">Alumni verification</v-btn>
      </a>
    </div>
  </v-col>
</template>

<script>
import SidebarItem from "./SidebarItem.vue";

export default {
  name: "SidebarDesktop",
  components: {
    SidebarItem,
  },
  methods: {
    goToHome() {
      this.$router.push({
        path: `/home`,
      });
    },
  },
};
</script>

<style scoped>
.sidebar {
  max-width: 287px;
  display: inline-flex;
  flex-direction: column;
  padding: 32px 24px 40px 24px;
  gap: 104px;
  border-right: 1px solid var(--Black-Scale-Black-50, #ccc);
  background: var(--Black-Scale-White, #fff);

  .sidebar-logo {
    max-width: 118px;
    height: auto;
    padding-left: 32px;
  }

  .sidebar-organism {
    display: flex;
    flex-direction: column;
    gap: 32px;

    .sidebar-molecule {
      display: flex;
      flex-direction: column;
      gap: 16px;

      .sidebar-title {
        font-size: 14px;
        font-weight: 500;
        line-height: 180%;
      }

      .sidebar-content {
        display: flex;
        flex-direction: column;
        gap: 4px;

        .sidebar-atoms {
          display: flex;
          width: 100%;
          padding: 12px 32px;
          align-items: center;
          gap: 12px;
          flex-direction: row;

          .logo {
            max-width: 24px;
            height: auto;
          }

          .typography {
            margin-bottom: 0;
            font-size: 18px;
            font-weight: 500;
            line-height: 180%;
            color: var(--Black-Scale-Black-300, #8f8f8f);
          }
        }

        .sidebar-atoms-highlighted {
          display: flex;
          width: 100%;
          padding: 12px 32px;
          align-items: center;
          gap: 12px;
          flex-direction: row;
          border-radius: 8px;
          background: linear-gradient(90deg, #0056d2 0%, #14a2ff 100%);

          .logo-highlighted {
            max-width: 24px;
            height: auto;
            color: white !important;
          }

          .typography-highlighted {
            margin-bottom: 0;
            font-size: 18px;
            font-weight: 500;
            line-height: 180%;
            color: white !important;
          }
        }
      }
    }
  }

  .alumni-card {
    display: flex;
    position: relative;
    height: 248px;
    min-width: 238px;
    max-width: 238px;
    max-height: 248px;
    padding: 24px 24px 26px 24px;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    border-radius: 16px;
    background: #fafbfc;

    .img {
      width: 200px;
      height: 228px;
      position: absolute;
      right: 19px;
      top: -19px;
    }

    .blur {
      width: 200px;
      height: 228px;
      position: absolute;
    }

    .button-alumni {
      max-width: 190px;
      height: 50px;
      padding: 4px 20px;
      border-radius: 4px;
      background: #0056d2;
      color: white !important;
      text-transform: capitalize;
      font-size: 14px;
      font-weight: 600;
    }
  }
}
</style>
