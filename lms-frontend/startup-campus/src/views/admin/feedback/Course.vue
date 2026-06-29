<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div>
    <Header />
    <div class="content-main-area">
      <Loading :isLoading="actLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />
      <div class="k-wrapping">
        <div class="k-dashboard">
          <Sidebar />
          <div class="k-content">
            <div class="k-card">
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>Course</h2>
              </div>
              <v-card>
                <div
                  class="f-form"
                  style="padding: 20px 15px 0; margin-bottom: -16px"
                >
                  <div class="f-label">Filter by Category</div>
                  <v-autocomplete
                    v-model="categories_id"
                    :items="cateData"
                    :item-text="getFilterSelectionText"
                    item-value="id"
                    placeholder="Select course category"
                    required
                    outlined
                    dense
                    clearable
                  ></v-autocomplete>
                </div>
                <v-card-title>
                  <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                  ></v-text-field>
                </v-card-title>
                <v-data-table
                  :headers="headers"
                  :items="datarow"
                  :loading="isLoading"
                  loading-text="Loading... Please wait"
                  :options.sync="options"
                  :server-items-length="totalData"
                >
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <router-link
                          :to="`/admin/tracking/feedback/${item.id}`"
                        >
                          <v-btn
                            class="mrb-8"
                            fab
                            x-small
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-format-list-bulleted-square
                            </v-icon>
                          </v-btn>
                        </router-link>
                      </template>
                      <span>See All Submission</span>
                    </v-tooltip>

                    <v-tooltip
                      bottom
                      v-if="
                        item.name === 'Journal Reflection' ||
                        item.name === 'Jurnal Reflective' ||
                        item.name === 'Jurnal Refleksi'
                      "
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <router-link
                          :to="`/admin/tracking/feedback/${item.id}/journal-reflection`"
                        >
                          <v-btn
                            class="mrb-8"
                            fab
                            x-small
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark> mdi-eye </v-icon>
                          </v-btn>
                        </router-link>
                      </template>
                      <span>See Journal Reflection</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from "@/components/HeaderCMS";
import Sidebar from "@/components/SidebarCMS";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
import DOMPurify from "dompurify";
export default {
  name: "CoursePage",
  metaInfo() {
    return {
      title: "Course - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {
    Header,
    Sidebar,
    Loading,
    Alert,
  },
  data() {
    return {
      isLoading: false,
      addLoading: false,
      dialog: false,
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      categories_id: "",
      cateData: [],

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,

      // table
      search: "",
      datarow: [],
      totalData: 0,
      options: {},
      typingTimer: 0,
      selectedItem: {},

      headers: [
        {
          text: "Name",
          value: "name",
          width: "40%",
        },
        {
          text: "Category",
          value: "course_category.name",
          width: "40%",
          sortable: false,
        },
        {
          text: "Action",
          value: "action",
          width: "20%",
          sortable: false,
        },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.getMainData();
      },
      deep: true,
    },
    search: {
      handler() {
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => {
          this.getMainData();
        }, 1000);
      },
      deep: true,
    },
    categories_id: {
      handler() {
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => {
          const routeCategoryId = this.$route.query.categories_id;
          if (this.categories_id == routeCategoryId) {
            return;
          }

          this.$router.push({ query: { categories_id: this.categories_id } });
          this.getMainData();
        }, 1000);
      },
      deep: true,
    },
  },
  methods: {
    titleCase(s) {
      return s
        .replace(/^[-_]*(.)/, (_, c) => c.toUpperCase()) // Initial char (after -/_)
        .replace(/[-_]+(.)/g, (_, c) => " " + c.toUpperCase()); // First char after each -/_
    },
    getFilterSelectionText(item) {
      return `${item.name} - ${this.titleCase(item.type)}`;
    },
    getMainData() {
      var search = this.search;
      var category_id = this.categories_id == null ? "" : this.categories_id;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0] ? "ASC" : "DESC";

      if (this.categories_id != null && this.options.sortBy[0] == null) {
        sortBy = "order";
        sortDesc = "ASC";
      }

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&categories_id=${category_id}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.Courses.rows;
            this.totalData = res.data.data.Courses.count;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    getCategoryData() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course-category?page=1&limit=9999999&sortBy=name&order=asc`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.cateData = res.data.data.CourseCategorys.rows;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    alert(item, group) {
      if (group == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = DOMPurify.sanitize(item); // Membersihkan nilai
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
    setRp(val) {
      let value = (val / 1).toFixed(0).replace(".", ",");
      // console.log("🚀 ~ file: Course.vue:506 ~ setRp ~ value", value);
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
  },
  mounted() {
    this.categories_id = this.$route.query.categories_id;
    this.$root.cms_tab = "tracking/feedback";
    this.getMainData();
    this.getCategoryData();
  },
};
</script>
