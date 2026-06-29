<template>
  <div class="sidebar">
    <div class="menu-cate"><span>Content Management System</span></div>
    <div class="menu">
      <div class="list">
        <router-link :to="`/admin/welcome`">
          <div class="left">
            <v-icon small> mdi-view-dashboard </v-icon>
          </div>
          <span>Dashboard</span>
        </router-link>
      </div>
      <div
        class="list"
        :class="{ active: this.$root.cms_tab == 'course-category' }"
      >
        <router-link :to="`/admin/course-category`">
          <div class="left">
            <v-icon small> mdi-notebook-check </v-icon>
          </div>
          <span>Course Category</span>
        </router-link>
      </div>
      <div class="list" :class="{ active: this.$root.cms_tab == 'course' }">
        <router-link :to="`/admin/course`">
          <div class="left">
            <v-icon small> mdi-book-open-page-variant </v-icon>
          </div>
          <span>Course & Lesson</span>
        </router-link>
      </div>
      <div class="list">
        <router-link :to="`/admin/expert`">
          <div class="left">
            <v-icon small> mdi-account-supervisor-circle-outline </v-icon>
          </div>
          <span>Expert List</span>
        </router-link>
      </div>
    </div>
    <div class="menu-cate"><span>Users Management</span></div>
    <div class="menu">
      <div class="list">
        <router-link :to="`/admin/users`">
          <div class="left">
            <v-icon small> mdi-account-group-outline </v-icon>
          </div>
          <span>Users</span>
        </router-link>
      </div>
    </div>
    <div class="menu">
      <div class="list">
        <router-link :to="`/admin/admins`">
          <div class="left">
            <v-icon small> mdi-shield-crown</v-icon>
          </div>
          <span>Admins</span>
        </router-link>
      </div>
    </div>
    <div class="menu-cate"><span>Tracking</span></div>
    <div class="menu">
      <div
        class="list"
        :class="{ active: this.$root.cms_tab == 'tracking/course-category' }"
      >
        <router-link :to="`/admin/tracking/course-category`">
          <div class="left">
            <v-icon small> mdi-progress-helper </v-icon>
          </div>
          <span>Enrollment & Progress</span>
        </router-link>
      </div>
    </div>
    <div class="menu">
      <div
        class="list"
        :class="{ active: this.$root.cms_tab == 'tracking/exam' }"
      >
        <router-link :to="`/admin/tracking/exam`">
          <div class="left">
            <v-icon small> mdi-pencil-ruler </v-icon>
          </div>
          <span>Exam</span>
        </router-link>
      </div>
    </div>
    <div class="menu">
      <div
        class="list"
        :class="{ active: this.$root.cms_tab == 'tracking/user' }"
      >
        <router-link :to="`/admin/tracking/user`">
          <div class="left">
            <v-icon small> mdi-account-details </v-icon>
          </div>
          <span>User Progress</span>
        </router-link>
      </div>
    </div>
    <div class="menu">
      <div
        class="list"
        :class="{ active: this.$root.cms_tab == 'tracking/feedback' }"
      >
        <router-link :to="`/admin/tracking/feedback`">
          <div class="left">
            <v-icon small> mdi-file-document </v-icon>
          </div>
          <span>Feedback LP</span>
        </router-link>
      </div>
    </div>
    <div class="menu-cate"><span>Others Management</span></div>
    <div class="menu">
      <div class="list">
        <router-link :to="`/admin/banner-promo`">
          <div class="left">
            <v-icon small> mdi-brightness-percent </v-icon>
          </div>
          <span>Banner Promo</span>
        </router-link>
      </div>
      <div class="list">
        <router-link :to="`/admin/voucher`">
          <div class="left">
            <v-icon small> mdi-ticket-percent-outline </v-icon>
          </div>
          <span>Voucher</span>
        </router-link>
      </div>
      <div class="list">
        <router-link :to="`/admin/exam-config`">
          <div class="left">
            <v-icon small> mdi-timer </v-icon>
          </div>
          <span>Exam Timer</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SidebarCMS",
  data() {
    return {
      undangan_id: "",
      isLoading: false,
      data: "",
    };
  },
  methods: {},
  mounted() {},
};
</script>

<style lang="scss" scoped>
.sidebar {
  width: 270px;
  min-height: calc(100vh - 150px);
  height: 100%;
  background: #fff;
  overflow-y: scroll;
  padding: 20px;
  box-shadow: 0 0 3px rgb(0 0 0 / 9%);
  margin-top: 20px;

  .user {
    margin-bottom: 24px;

    .desc {
      font-size: 13px;
      margin-bottom: 8px;
    }
  }

  .menu-cate {
    font-size: 12px;
    color: #000;
    position: relative;
    margin-bottom: 16px;

    span {
      background: #fff;
      position: relative;
      padding-right: 12px;
    }
  }

  .menu-cate::before {
    content: "";
    background: #000;
    height: 1px;
    width: 100%;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    right: 0;
  }

  .menu {
    .list.active {
      span {
        font-weight: 600;
        color: #0056d2;
      }

      .left {
        .v-icon {
          color: #0056d2;
        }
      }
    }
    .list {
      :hover {
        span {
          font-weight: 600;
          color: #0056d2;
        }

        .left {
          .v-icon {
            color: #0056d2;
          }
        }
      }

      a {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        position: relative;
        cursor: pointer !important;

        .left {
          line-height: 0;

          .v-icon {
            color: #000;
          }
        }

        span {
          margin-left: 16px;
          font-size: 14px;
          color: #000;
          transition: all 0.1s ease-in-out;
        }
      }

      a.router-link-exact-active {
        .left {
          line-height: 0;

          .v-icon {
            color: #0056d2;
          }
        }

        span {
          color: #0056d2;
          font-weight: 700;
        }
      }
    }
  }
}
</style>
