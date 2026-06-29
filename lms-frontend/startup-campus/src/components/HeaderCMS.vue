<template>
  <div class="header">
    <div class="k-wrapping">
      <div class="content">
        <router-link to="/user/undangan-list">
          <img src="@/assets/img/logo.png" alt="" />
        </router-link>
        <div class="menu">
          <a @click="logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "HeaderCMS",
  data() {
    return {
      token: "",
    };
  },

  methods: {
    logout() {
      this.$root.token(false);
      this.$router.replace("/admin");
    },
  },
  mounted() {
    var token = this.$root.token();
    if (!token) {
      // console.log(token);
      this.$root.token(false);
      this.$router.replace("/admin");
    }
  },
};
</script>
<style lang="scss" scoped>
.header {
  box-shadow: 1px 5px 20px 0px rgb(186 186 186 / 26%);
  padding: 10px 0px;
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  background: #fff;
  z-index: 9;

  .k-wrapping {
    padding: 10px 100px;
  }

  .content {
    display: flex;
    align-items: center;
    justify-content: space-between;

    img {
      width: 150px;
    }

    .menu {
      display: flex;
      align-items: center;

      a {
        margin-left: 24px;
      }
    }
  }
}
</style>
