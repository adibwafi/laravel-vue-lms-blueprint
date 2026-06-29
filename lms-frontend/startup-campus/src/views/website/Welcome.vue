<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">welcome</div>
  </v-fade-transition>
</template>

<script>
export default {
  name: "VerifyPage",
  metaInfo() {
    return {
      title: "Verify - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {},
  data: () => ({}),
  computed: {},
};
</script>

<style lang="scss" scoped>
.main-container {
}
</style>
