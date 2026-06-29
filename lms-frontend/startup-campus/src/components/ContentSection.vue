<template>
  <div v-html="content" class="content" id="myContent"></div>
</template>

<script>
import Vue from "vue";
import debounce from "lodash/debounce";
import ZoomableImage from "@/components/ZoomableImage.vue";

export default {
  name: "ContentSection",
  props: {
    content: {
      type: String,
      // required: true,
    },
  },
  data() {
    return {
      modifiedElements: "",
    };
  },
  watch: {
    // content() {
    //   this.modifiedElements = this.content;
    //   this.modifyContent();
    // },
  },
  methods: {
    modifyContent: function () {
      // console.log("Modifying content");
      const anchors = this.$el.getElementsByTagName("img");
      // console.log(Array.from(anchors), Array.from(anchors).length);

      Array.from(anchors).forEach((anchor) => {
        const image = anchor;

        const src = image.getAttribute("src");
        const height = image.getAttribute("height") ?? "100%";
        const width = image.getAttribute("width") ?? "auto";
        // https://vuejs.org/v2/api/#propsData
        const propsData = {
          src,
          height,
          width,
        };
        // https://vuejs.org/v2/api/#parent
        // Without parent context RouterLink will be unable to access this.$router, etc.
        const parent = this;
        const children = image;

        // const VueZoomer = Vue.component(ZoomableImage);
        const VueZoomer = Vue.extend(ZoomableImage);
        // console.log(VueZoomer);
        const zoomer = new VueZoomer({ propsData, parent, children });
        zoomer.$mount(anchor); // Replaces anchor element
        zoomer.$children = image;
        // console.log(zoomer);
      });
    },
  },
  updated() {
    debounce(
      () => {
        // console.log("Updated");
        this.modifyContent();
      },
      2000,
      {
        leading: true,
        trailing: false,
      }
    )();
  },
};
</script>

<style lang="scss" scoped>
.content {
  font-size: 16px;
  word-wrap: break-word;

  ::v-deep .tooltip {
    font-weight: 700;
    text-decoration: underline;
    cursor: pointer;
  }

  ::v-deep .fill::before {
    position: absolute;
    background: #ffffff;
    box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
    border-radius: 5px;
    width: 100%;
    height: calc(100% + 2px);
    content: "";
  }

  ::v-deep .fill {
    font-weight: 700;
    cursor: pointer;
    position: relative;
    display: inline-block;
  }

  ::v-deep .fill.true {
    color: #57ea5d;
  }

  ::v-deep .fill.true::before {
    display: none;
  }

  ::v-deep .fill.false::before {
    border: 1px solid #ea5757;
  }

  ::v-deep .fill::before {
    position: absolute;
    background: #ffffff;
    box-shadow: inset 0px 0px 10px rgba(43, 44, 39, 0.25);
    border-radius: 5px;
    width: 100%;
    height: calc(100% + 2px);
    content: "";
  }
}
</style>
