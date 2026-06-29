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
                <h2>Course Category</h2>
                <router-link
                  class="btn btn-blue btn-sm"
                  to="/admin/course-category/add"
                >
                  Add New
                </router-link>
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
                  <template v-slot:[`item.banner`]="{ item }">
                    <div class="dataimg">
                      <img
                        :src="`${storageBaseUrl}${item.banner}`"
                        alt=""
                        class="imgs"
                      />
                    </div>
                  </template>
                  <template v-slot:[`item.type`]="{ item }">
                    <div>{{ convertType(item.type) }}</div>
                  </template>
                  <template v-slot:[`item.price`]="{ item }">
                    <div>{{ item.price > 0 ? setRp(item.price) : "FREE" }}</div>
                  </template>
                  <template v-slot:[`item.action`]="{ item }">
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
        <!-- Modal Delete item -->
        <v-dialog
          v-model="dialogDelete"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogDelete">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Apakah kamu yakin ingin menghapus course category ini? Semua
                data course juga akan ikut terhapus
                {{
                  selectedItem.item
                    ? '"' + selectedItem.item + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div
                  :style="{
                    marginTop: '24px',
                  }"
                >
                  <p>List data course yang akan terhapus:</p>
                  <ol v-if="!getDeletedCourseLoading">
                    <li v-for="course in deletedCourses" v-bind:key="course.id">
                      {{ course.name }}
                    </li>
                  </ol>
                  <div v-else>
                    <p>Loading...</p>
                  </div>
                </div>
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
  name: "CourseCategoryPage",
  metaInfo() {
    return {
      title: "Course Category - AcmeLMS",
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
      dialogDelete: false,
      id: "",
      status: "",
      statusItems: ["draft", "released", "disabled"],

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,

      // table
      search: "",
      datarow: [],
      totalData: 0,
      options: {},
      typingTimer: 0,
      selectedItem: {},
      getDeletedCourseLoading: false,
      deletedCourses: [],

      headers: [
        {
          text: "Banner",
          value: "banner",
          align: "start",
          width: "10%",
          sortable: false,
        },
        {
          text: "Name",
          value: "name",
          width: "30%",
        },
        {
          text: "Type",
          value: "type",
          width: "10%",
        },
        {
          text: "Price",
          value: "price",
          width: "20%",
        },
        {
          text: "Status",
          value: "status",
          width: "15%",
        },
        {
          text: "Action",
          value: "action",
          width: "25%",
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
    convertType(type) {
      const ret = type
        .split("_")
        .filter((x) => x.length > 0)
        .map((x) => x.charAt(0).toUpperCase() + x.slice(1))
        .join(" ");
      return ret;
    },
    getDeletedCourse(id) {
      this.getDeletedCourseLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course-category/read/${id}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            const courseCategory = res.data.data.CourseCategory;
            // console.log(
            //   "🚀 ~ file: CourseCategory.vue:310 ~ .then ~ course:",
            //   courseCategory
            // );

            this.deletedCourses = courseCategory.course;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.getDeletedCourseLoading = false;
        });
    },
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
          url: `/v1/admin/course-category?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.CourseCategorys.rows;
            this.totalData = res.data.data.CourseCategorys.count;
            // console.log(this.datarow);
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

    editStatus(item) {
      this.selectedItem = item;
      this.dialog = true;
      this.status = item.status;
      this.id = item.id;
    },

    goEdit(item) {
      this.$router.push({
        path: `/admin/course-category/${item.id}/edit`,
      });
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
      this.getDeletedCourse(item.id);
    },

    moveUp(item) {
      this.isLoading = true;
      this.id = item.id;
      this.$root
        .axios({
          method: "put",
          url: `/api/v1/admin/homepages/banner/${this.id}/move-up`,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.getMainData();
        });
    },

    moveDown(item) {
      this.isLoading = true;
      this.id = item.id;
      this.$root
        .axios({
          method: "put",
          url: `/api/v1/admin/homepages/banner/${this.id}/move-down`,
        })
        .then((res) => {
          if (res.data.success) {
            this.alert(res.data.message, "success");
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.getMainData();
        });
    },

    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
    },

    submitStatus() {
      this.isLoading = true;
      var formData = new FormData();
      formData.append("status", DOMPurify.sanitize(this.status)); // Membersihkan nilai
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/course-category/update/${this.id}`,
          data: formData,
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
          this.dialog = false;
          this.getMainData();
        });
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/course-category/delete/${this.id}`,
        })
        .then((res) => {
          // console.log(res);
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
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
  },
  mounted() {
    this.getMainData();
    this.$root.cms_tab = "course-category";
  },
};
</script>
