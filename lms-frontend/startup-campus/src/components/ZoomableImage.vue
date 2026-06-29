<template>
  <div class="container">
    <v-zoomer
      ref="zoomer"
      :style="{ width: width, height: height }"
      :limitTranslation="true"
      :mouseWheelToZoom="false"
      :zoomed.sync="zoomed"
      class="zoomable-image"
    >
      <img
        :src="src"
        style="object-fit: contain; width: 100%; height: 100%"
        :style="{ cursor: zoomed ? 'zoom-out' : 'zoom-in' }"
        :draggable="false"
      />
    </v-zoomer>
    <div class="toolbar" v-show="zoomed">
      <v-btn x-small @click="$refs.zoomer.reset()">Reset</v-btn>
    </div>
  </div>
</template>

<script>
import VueZoomer from "vue-zoomer";

export default {
  name: "ZoomableImage",
  data() {
    return { zoomed: false };
  },
  props: {
    src: {
      type: String,
      required: true,
    },
    width: {
      required: true,
    },
    height: {
      required: true,
    },
  },
  components: {
    VZoomer: VueZoomer.Zoomer,
  },
};
</script>

<style lang="scss" scoped>
.container {
  position: relative;
  padding: 12px 0px;

  .toolbar {
    position: absolute;
    bottom: 24px;
    right: 24px;
  }
}

img {
  user-select: auto;
}

img:active {
  cursor: grabbing;
  cursor: -moz-grabbing;
  cursor: -webkit-grabbing;
}
</style>
