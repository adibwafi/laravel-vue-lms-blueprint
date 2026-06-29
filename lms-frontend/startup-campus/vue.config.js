const { defineConfig } = require("@vue/cli-service");
module.exports = defineConfig({
  transpileDependencies: ["vuetify"],
  configureWebpack: { output: { filename: "[name].[hash].bundle.js" } },
  filenameHashing: true,
});
