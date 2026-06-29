<template>
  <div class="header"></div>
</template>

<script>
export default {
  name: "HeaderApp",
  data() {
    return {
      token: "",
    };
  },

  methods: {
    // logout() {
    //   this.$root.token(false);
    //   this.$router.replace("/admin");
    // },
  },
  mounted() {
    // var token = this.$root.token();
    // console.log(token);
    // if (!token) {
    //   this.$root.token(false);
    //   this.$router.replace("/");
    // }
  },
};
</script>
<style lang="scss" scoped></style>
