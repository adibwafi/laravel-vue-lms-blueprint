/* eslint-disable prettier/prettier */
import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import axios from "axios";
import "@/assets/css/styles.scss";
import DOMPurify from "dompurify";
import CKEditor from "@ckeditor/ckeditor5-vue2";

import VueZoomer from "vue-zoomer";
import VueMeta from "vue-meta";
import Cookies from "universal-cookie";

if (!process.env.VUE_APP_API_BASE_URL) {
  console.error("VUE_APP_API_BASE_URL is not defined in .env file");
  throw new Error("VUE_APP_API_BASE_URL is not defined in .env file");
}
var baseURL = process.env.VUE_APP_API_BASE_URL;
axios.defaults.baseURL = baseURL;
axios.defaults.withCredentials = true;
axios.defaults.headers["Content-Type"] = "application/json";

Vue.use(CKEditor);
Vue.use(VueZoomer);
Vue.use(VueMeta, {
  // optional pluginOptions
  refreshOnceOnNavigation: true,
});
Vue.config.productionTip = false;

function redirectLogin() {
  const cookies = new Cookies();
  const options = {
    domain: getCookieDomain(),
    expires: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
  };

  if (window.location.origin.includes("localhost")) {
    options.domain = "localhost";
  }
  cookies.remove("token", options);
  cookies.remove("userId", options);
  cookies.remove("user", options);
  axios.defaults.headers["Authorization"] = `Bearer :null`;

  if (window.location.href !== "/") {
    const pathname = window.location.pathname;
    const queryParam = `?redirect=${pathname}`;
    window.location.href = "/" + queryParam;
  }
}

function getCookieDomain() {
  const parsedUrl = new URL(window.location.href);
  return parsedUrl.hostname; 
}

// Add a 401 and 403 response interceptor
axios.interceptors.response.use(
  function (response) {
    return response;
  },
  function (error) {
    if (401 === error.response.status) {
      redirectLogin();
    } else if (403 === error.response.status) {
      alert("You are not authorized to access this page");
      window.location.href = "/home";
    } else {
      return Promise.reject(error);
    }
  }
);

// Register DOMPurify as a global plugin
Vue.prototype.$DOMPurify = DOMPurify;

new Vue({
  router,
  vuetify,
  data: {
    cms_tab: "",
    baseURL: baseURL,
    storageBaseURL: process.env.VUE_APP_STORAGE_BASE_URL,
    fullname: "",
  },
  methods: {
    redirectLogin: redirectLogin,
    token: function (key) {
      const cookies = new Cookies();
      const options = {
        domain: getCookieDomain(),
        expires: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
      };

      if (key === false) {
        cookies.remove("token", options);
        axios.defaults.headers["Authorization"] = `Bearer :null`;
        return false;
      }
      if (typeof key != "undefined") {
        cookies.set("token", key, options);
        axios.defaults.headers["Authorization"] = `Bearer ${key}`;
        return key;
      }
      var v = cookies.get("token", options);
      if (!v || v === "" || v === "false") {
        axios.defaults.headers["Authorization"] = `Bearer :null`;
        return false;
      }
      return v;
    },
    userId: function (key) {
      const cookies = new Cookies();
      const options = {
        domain: getCookieDomain(),
        expires: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
      };

      if (key === false) {
        cookies.remove("userId", options);
        return undefined;
      }
      if (typeof key != "undefined") {
        cookies.set("userId", key, options);
        return key;
      }
      var v = cookies.get("userId", options);
      if (!v || v === "" || v === "false") {
        return undefined;
      }
      return v;
    },
    user: function (key) {
      const cookies = new Cookies();
      const options = {
        domain: getCookieDomain(),
        expires: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
      };
      if (key === false) {
        cookies.remove("user", options);
        return undefined;
      }
      if (typeof key != "undefined") {
        const userString = JSON.stringify(key);
        cookies.set("user", userString, options);
        return key;
      }
      var v = cookies.get("user", options);
      if (!v || v === "" || v === "false") {
        return undefined;
      }
      return JSON.parse(v);
    },
    axios: axios,
    upload: function (method, url, form) {
      return this.axios({
        method: method,
        url: url,
        data: form,
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
    },
    // csrfCookie: function () {

    // }
  },
  created() {
    const token = this.token()
    axios.defaults.headers["Authorization"] = `Bearer ${token}`;
  },
  render: (h) => h(App),
}).$mount("#app");
