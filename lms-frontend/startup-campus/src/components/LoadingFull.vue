<template>
  <div class="loading" v-if="isLoading">
    <div class="is-loading">
      <img src="@/assets/img/icn-logo-w.png" alt="" class="logo" />
      <div class="text">Please wait</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "isLoading",
  props: ["isLoading"],
  data() {
    return {};
  },
  mounted() {},
};
</script>

<style lang="scss" scoped>
.loading {
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  background: #fff;
  width: 100%;
  height: 100vh;
  z-index: 99;
  max-width: 414px;
}
.is-loading {
  //   max-width: 414px;
  margin: 0 auto;
  background: #0056d2;
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  padding: 14px;
  border-radius: 12px;
  text-align: center;
  z-index: 99;

  .logo {
    width: 50px;
    -webkit-animation-name: scaleIn;
    -webkit-animation-duration: 1.5s;
    animation-name: scaleIn;
    animation-duration: 1.5s;
    animation-iteration-count: infinite;
    -webkit-animation-iteration-count: infinite;
  }

  .text {
    font-size: 12px;
    color: #fff;
  }
}
</style>
