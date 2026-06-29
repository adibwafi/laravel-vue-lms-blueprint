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
                <h2>Users Course</h2>
                <button class="btn btn-blue btn-sm" @click="openDialog()">
                  Enroll Course
                </button>
              </div>
              <v-card>
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
                  <template v-slot:[`item.course_category.banner`]="{ item }">
                    <div class="dataimg">
                      <img
                        v-if="item.course_category.banner"
                        :src="`${storageBaseUrl}` + item.course_category.banner"
                        alt=""
                        class="imgs"
                      />
                      <img
                        src="@/assets/img/img-placeholder.jpeg"
                        alt=""
                        v-else
                      />
                    </div>
                  </template>
                  <template v-slot:[`item.created_at`]="{ item }">
                    <div class="dataimg">
                      {{ date(item.created_at) }}
                    </div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
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
                          @click="openDialog(item)"
                        >
                          <v-icon dark> mdi-pencil </v-icon>
                        </v-btn>
                      </template>
                      <span>Edit</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="red"
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
        <!-- Modal Block / Unblock item -->
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
                  selectedItem.course_category.name
                    ? '"' + selectedItem.course_category.name + '"'
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

        <v-dialog
          v-model="dialog"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <v-card>
            <v-toolbar color="#09408e" dark>
              {{ id ? "Edit" : "Enroll" }} Course</v-toolbar
            >
            <v-card-text>
              <div
                class="f-form"
                style="padding: 20px 15px 0; margin-bottom: -16px"
              >
                <div class="f-label">Select Course Category</div>
                <v-autocomplete
                  v-model="course_category_id"
                  :items="cateData"
                  item-text="name"
                  item-value="id"
                  placeholder="Select course category"
                  required
                  outlined
                  dense
                  clearable
                ></v-autocomplete>
              </div>
              <div class="btn-duo mt-6">
                <button
                  v-if="id"
                  @click="update()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Update" }}
                </button>
                <button
                  v-else
                  @click="submit()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Save" }}
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
import moment from "moment";
export default {
  name: "UserCoursePage",
  metaInfo() {
    return {
      title: "User Course - AcmeLMS",
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
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      isLoading: false,
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      dialogUnblock: false,
      id: "",
      userId: "",
      course_category_id: "",
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
          text: "Image",
          align: "start",
          value: "course_category.banner",
          width: "10%",
        },
        {
          text: "Name",
          value: "course_category.name",
          width: "45%",
        },
        {
          text: "Date",
          value: "created_at",
          width: "20%",
        },
        {
          text: "Action",
          value: "action",
          width: "15%",
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
  },
  methods: {
    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0] ? "ASC" : "DESC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/user-course-category?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&user_id=${this.userId}&with_course_category=true`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.datarow = res.data.data.UserCoursesCategory.rows;
            this.totalData = res.data.data.UserCoursesCategory.count;
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

    date(val) {
      return moment(new Date(val)).format("DD MMMM YYYY");
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
      // console.log(this.id);
    },

    submit() {
      this.addLoading = true;
      var data = {
        course_category_id: this.course_category_id,
        user_id: this.userId,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/user-course-category/create`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
          } else {
            this.alert(res.data.message, "danger");
          }
          this.getMainData();
          this.addLoading = false;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialog = false;
        });
    },

    update() {
      this.addLoading = true;
      var data = {
        course_category_id: this.course_category_id,
        user_id: this.userId,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/user-course-category/update/${this.id}`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
          } else {
            this.alert(res.data.message, "danger");
          }
          this.getMainData();
          this.addLoading = false;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialog = false;
        });
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/user-course-category/delete/${this.id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
          } else {
            this.alert(res.data.message, "danger");
          }
          this.getMainData();
          this.isLoading = false;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.dialogDelete = false;
        });
    },

    getCategoryData() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/course-category?page=1&limit=9999999&sortBy=name&order=asc`,
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
        this.course_category_id = "";
      } else {
        this.id = item.id;
        this.course_category_id = item.course_category_id;
      }
      this.dialog = true;
      this.getCategoryData();
    },

    alert(item, group) {
      if (group == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = item;
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
  mounted() {
    this.userId = this.$route.params.id;
    this.getMainData();
    this.$root.cms_tab = "default";
  },
};
</script>
