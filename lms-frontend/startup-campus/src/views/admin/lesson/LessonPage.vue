!-- eslint-disable vue/multi-word-component-names -->
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
              <div class="breadcrumbs">
                <router-link to="/admin/course">{{
                  dataParent?.course?.name
                }}</router-link>
                <router-link :to="`/admin/course/${this.course_id}/lesson`">{{
                  dataParent.name
                }}</router-link>
                <div class="active">Lesson Page</div>
              </div>
              <div
                class="k-head d-flex align-items-center justify-space-between"
              >
                <h2>{{ dataParent.name }} - Lesson Page</h2>
                <router-link
                  :to="`/admin/course/${course_id}/lesson/${lesson_id}/page/add`"
                  class="btn btn-blue btn-sm"
                  >Add New</router-link
                >
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
                  <template v-slot:[`item.action`]="{ item }">
                    <v-btn
                      class="mrb-8"
                      v-if="datarow.indexOf(item) != datarow.length - 1"
                      fab
                      x-small
                      @click="moveDown(item)"
                    >
                      <v-icon dark> mdi-chevron-down </v-icon>
                    </v-btn>
                    <v-btn
                      class="mrb-8"
                      v-if="datarow.indexOf(item) != 0"
                      fab
                      x-small
                      @click="moveUp(item)"
                    >
                      <v-icon dark> mdi-chevron-up </v-icon>
                    </v-btn>
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
                          @click="goEdit(item.id)"
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
      </div>
    </div>
  </div>
</template>

<script>
import Header from "@/components/HeaderCMS";
import Sidebar from "@/components/SidebarCMS";
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
export default {
  name: "LessonPage",
  metaInfo() {
    return {
      title: "Lesson - AcmeLMS",
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
      course_id: "",
      lesson_id: "",
      id: "",
      isScope: false,

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,
      maxSorter: 0,

      // table
      search: "",
      datarow: [],
      totalData: 0,
      options: {},
      typingTimer: 0,
      selectedItem: {},
      dataParent: "",

      headers: [
        {
          text: "Name",
          value: "name",
          align: "start",
          width: "50%",
        },
        {
          text: "Content Type",
          value: "content_type",
          sortable: false,
          width: "25%",
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
    getParentData(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/lesson/read/${id}`,
        })
        .then((res) => {
          // console.log("parent", res);
          if (res.data.success) {
            this.dataParent = res.data.data.Lesson;
            // console.log(
            //   "🚀 ~ file: LessonPage.vue:239 ~ .then ~ dataParent:",
            //   this.dataParent
            // );
            return;
          } else {
            this.$router.push({
              path: `/admin/course/${this.course_id}/lesson`,
            });
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0] ? this.options.sortBy[0] : "sorter";
      var sortDesc = this.options.sortDesc[0]
        ? this.options.sortDesc[0]
        : "ASC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/lesson-page?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&lessons_id=${this.$route.params.id}`,
        })
        .then((res) => {
          // console.log("🚀 ~ file: LessonPage.vue:266 ~ .then ~ res:", res);
          if (!res.data.error) {
            this.datarow = res.data.data.LessonPages.rows;
            const sorter = this.datarow.map((t) => t.sorter);
            const maxSorter = Math.max(...sorter);
            this.maxSorter = maxSorter;
            // console.log(sorter);
            // console.log(this.maxSorter);
            this.totalData = res.data.data.LessonPages.count;
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

    moveUp(item) {
      this.isLoading = true;
      var data = {
        id: item.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/lesson-page/moveup`,
          data: data,
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
      var data = {
        id: item.id,
      };
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/lesson-page/movedown`,
          data: data,
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
          url: `/v1/admin/lesson-page/delete/${this.id}`,
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

    goEdit(id) {
      this.$router.push({
        path: `/admin/course/${this.course_id}/lesson/${this.lesson_id}/page/${id}/edit`,
      });
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
    this.getMainData();
    var course_id = this.$route.params.course_id;
    var lesson_id = this.$route.params.id;
    if (course_id && lesson_id) {
      this.course_id = course_id;
      this.lesson_id = lesson_id;
      this.getParentData(lesson_id);
      this.isScope = this.$route.query.scope ? true : false;
    }
    this.$root.cms_tab = "course";
  },
};
</script>
