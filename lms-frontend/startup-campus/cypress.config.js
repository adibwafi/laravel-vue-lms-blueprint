const { defineConfig } = require("cypress");
require("dotenv").config();
module.exports = defineConfig({
  env: process.env,
  e2e: {
    baseUrl: process.env.CYPRESS_BASE_URL,
    setupNodeEvents() {
      // implement node event listeners here
    },
  },
});
