<template>
  <div>
    <div class="app-header">
      <template v-if="isLoading"> Loading... </template>

      <template v-else>
        <span v-if="showAllPages"> {{ pageCount }} page(s) </span>

        <span v-else>
          <button :disabled="page <= 1" @click="page--">❮</button>

          {{ page }} / {{ pageCount }}

          <button :disabled="page >= pageCount" @click="page++">❯</button>
        </span>

        <div class="toolbar">
          <label class="check">
            <input v-model="showAllPages" type="checkbox" />
            Show all pages
          </label>
          <label class="right">
            <a :href="source" target="_blank" rel="noreferrer noopener">
              <v-btn small fab color="white" plain dark>
                <v-icon>mdi-fullscreen</v-icon>
              </v-btn>
            </a>
          </label>
        </div>
      </template>
    </div>

    <div class="app-content">
      <vue-pdf-embed
        ref="pdfRef"
        :source="source"
        :page="page"
        :disableAnnotationLayer="true"
        :scale="getScale()"
        @password-requested="handlePasswordRequest"
        @rendered="handleDocumentRender"
      />
    </div>
  </div>
</template>

<script>
// import VuePdfEmbed from "vue-pdf-embed";

// OR THE FOLLOWING IMPORT FOR VUE 2
import VuePdfEmbed from "vue-pdf-embed/dist/vue2-pdf-embed";

export default {
  name: "PdfViewer",
  props: {
    source: {
      type: String,
      required: true,
    },
  },
  components: {
    VuePdfEmbed,
  },
  data() {
    return {
      isLoading: true,
      page: 1,
      pageCount: 1,
      showAllPages: false,
    };
  },
  watch: {
    showAllPages() {
      this.page = this.showAllPages ? null : 1;
    },
  },
  methods: {
    getScale() {
      return window.devicePixelRatio;
    },
    handleDocumentRender() {
      // console.log(args);
      this.isLoading = false;
      this.pageCount = this.$refs.pdfRef.pageCount;
    },

    handlePasswordRequest(callback, retry) {
      callback(prompt(retry ? "Enter password again" : "Enter password"));
    },
  },
};
</script>

<style scoped>
body {
  margin: 0;
  padding: 0;
  background-color: #ccc;
}

.vue-pdf-embed > div {
  margin-bottom: 8px;
  box-shadow: 0 2px 8px 4px rgba(0, 0, 0, 0.1);
}

.app-header {
  padding: 16px;
  box-shadow: 0 2px 8px 4px rgba(0, 0, 0, 0.1);
  background-color: #0056d2;
  color: #ddd;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.toolbar .check {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
}
</style>
