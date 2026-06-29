<template>
  <v-app>
    <LoadingScreen :isSplash="isSplash" />
    <v-main v-if="!isSplash">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
import LoadingScreen from "./components/SplashScreen";
export default {
  metaInfo: {
    title: "Startup Campus",
    // titleTemplate: "%s - Yay!",
  },
  name: "App",
  components: {
    LoadingScreen,
  },
  data() {
    return { isSplash: true };
  },
  mounted() {
    setTimeout(() => {
      this.isSplash = false;
    }, 1000);
  },
};
</script>
