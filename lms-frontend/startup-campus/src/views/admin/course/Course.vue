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
                <router-link to="/admin/course/add" class="btn btn-blue btn-sm"
                  >Add New</router-link
                >
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
                  <template v-slot:[`item.banner`]="{ item }">
                    <div class="dataimg">
                      <img
                        :src="`${storageBaseUrl}${item.banner}`"
                        alt=""
                        class="imgs"
                      />
                    </div>
                  </template>
                  <template v-slot:[`item.price`]="{ item }">
                    <div>{{ item.price > 0 ? setRp(item.price) : "FREE" }}</div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <router-link :to="`/admin/course/${item.id}/lesson`">
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
                      <span>See All Lesson</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <router-link :to="`/admin/course/${item.id}/exam`">
                          <v-btn
                            class="mrb-8"
                            fab
                            x-small
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark> mdi-sticker-text </v-icon>
                          </v-btn>
                        </router-link>
                      </template>
                      <span>Add Exam</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="#0056d2"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="editStatus(item)"
                        >
                          <v-icon color="white"> mdi-archive-eye </v-icon>
                        </v-btn>
                      </template>
                      <span>Edit Status</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="teal"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="goEdit(item)"
                        >
                          <v-icon dark> mdi-pencil </v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="pink"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="deleteItem(item)"
                        >
                          <v-icon dark> mdi-delete </v-icon>
                        </v-btn>
                      </template>
                      <span>Delete</span>
                    </v-tooltip>
                  </template>
                </v-data-table>
              </v-card>
            </div>
          </div>
        </div>
        <!-- Modal Delete item -->
        <v-dialog
          v-model="dialogDelete"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogDelete">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to delete
                {{
                  selectedItem.item
                    ? '"' + selectedItem.item + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitDelete()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Delete" }}
                  </button>
                  <a
                    @click="dialogDelete.value = false"
                    class="btn btn-text btn-sm tf"
                    >Cancel</a
                  >
                </div>
              </v-card-text>
            </v-card>
          </template>
        </v-dialog>
        <!-- Modal Add / Edit item -->
        <v-dialog
          v-model="dialog"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark>
              Edit Status {{ selectedItem.name }}</v-toolbar
            >
            <v-card-text>
              <v-form class="mt-24">
                <v-select
                  v-model="status"
                  :items="statusItems"
                  label="Select Status"
                ></v-select>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  @click="submitStatus()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Update" }}
                </button>
                <a @click="dialog = false" class="btn btn-text btn-sm tf"
                  >Cancel</a
                >
              </div>
            </v-card-text>
          </v-card>
        </v-dialog>
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
      dialogDelete: false,
      id: "",
      name: "",
      status: "",
      statusItems: ["draft", "released", "disabled"],
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
        // {
        //   text: "Banner",
        //   value: "banner",
        //   align: "start",
        //   width: "10%",
        //   sortable: false,
        // },
        {
          text: "Name",
          value: "name",
          width: "25%",
        },
        {
          text: "Category",
          value: "course_category.name",
          width: "15%",
          sortable: false,
        },
        {
          text: "Expert",
          value: "tutor[0].name",
          width: "20%",
          sortable: false,
        },
        {
          text: "Status",
          value: "status",
          width: "10%",
        },
        {
          text: "Action",
          value: "action",
          width: "25%",
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

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.name = "";
        this.color = "";
      } else {
        this.id = item.id;
        this.name = item.name;
        this.color = item.color;
      }
      this.dialog = true;
    },

    editStatus(item) {
      this.selectedItem = item;
      this.dialog = true;
      this.status = item.status;
      this.id = item.id;
    },

    submitStatus() {
      this.isLoading = true;
      var formData = new FormData();
      formData.append("status", DOMPurify.sanitize(this.status)); // Membersihkan nilai
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/course/update/${this.id}`,
          data: formData,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(DOMPurify.sanitize(res.data.message), "success"); // Membersihkan nilai
            return;
          } else {
            this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialog = false;
          this.getMainData();
        });
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/course/delete/${this.id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          } else {
            this.alert(res.data.message, "danger");
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialogDelete = false;
          this.getMainData();
        });
    },

    seeLesson(item) {
      this.$router.push({
        path: `/admin/course/${item.id}/lesson`,
      });
    },
    seeExam(item) {
      this.$router.push({
        path: `/admin/course/${item.id}/exam`,
      });
    },

    goEdit(item) {
      this.$router.push({
        path: `/admin/course/${item.id}/edit`,
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
    this.$root.cms_tab = "course";
    this.getCategoryData();
  },
};
</script>
