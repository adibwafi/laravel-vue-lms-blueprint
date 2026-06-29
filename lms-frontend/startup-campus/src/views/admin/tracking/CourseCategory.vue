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
                <h2>Tracking Course Category</h2>
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
                  <template
                    v-slot:[`item.tracking.average_percentage_completed`]="{
                      item,
                    }"
                  >
                    <div>
                      {{
                        parseFloat(
                          item.tracking.average_percentage_completed
                        ).toFixed(2)
                      }}
                      %
                    </div>
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
                          @click="viewEnroll(item)"
                        >
                          <v-icon color="white">
                            mdi-eye-arrow-left-outline
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Peserta</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          class="mrb-8"
                          color="green"
                          fab
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="viewCourse(item)"
                        >
                          <v-icon color="white">
                            mdi-book-open-page-variant
                          </v-icon>
                        </v-btn>
                      </template>
                      <span>Lihat Course</span>
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
          text: "Jumlah Peserta",
          value: "tracking.number_enrolled_user",
          width: "15%",
        },
        {
          text: "Rata-Rata Progress Keseluruhan Peserta",
          value: "tracking.average_percentage_completed",
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
    viewEnroll(item) {
      this.$router.push(`/admin/tracking/course-category/${item.id}/enroll`);
    },
    viewCourse(item) {
      this.$router.push(`/admin/tracking/course-category/${item.id}/course`);
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
          url: `/v1/admin/course-category?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&with_tracking=true`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.CourseCategorys.rows;
            this.totalData = res.data.data.CourseCategorys.count;
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

    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
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
    this.$root.cms_tab = "tracking/course-category";
  },
};
</script>
