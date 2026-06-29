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
                <h2>Voucher</h2>
                <button class="btn btn-blue btn-sm" @click="openDialog()">
                  Add New
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
                  <template v-slot:[`item.is_redeemed`]="{ item }">
                    <v-simple-checkbox
                      :v-model="!!item.is_redeemed"
                      :value="!!item.is_redeemed"
                      label="blue"
                      color="blue"
                    ></v-simple-checkbox>
                  </template>
                  <template v-slot:[`item.course_category_id`]="{ item }">
                    {{ item.course_category.name }} -
                    {{ item.course_category.type }}
                  </template>
                  <template v-slot:[`item.redeemed_by`]="{ item }">
                    {{ item.redeemed_by ? item.redeemed_by.email : "-" }}
                  </template>
                  <template v-slot:[`item.created_by`]="{ item }">
                    {{ item.created_by.email }}
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
              {{ id ? "Edit Item" : "Add New" }}</v-toolbar
            >
            <v-card-text class="dialog-form">
              <v-form>
                <!-- <v-text-field
                  v-model="token"
                  label="Token"
                  class="input"
                  placeholder="Enter token"
                  :rules="tokenRules"
                  hint="Token harus berisi 10 karakter, hanya mengandung huruf besar + angka, dan tidak boleh sama dengan voucher yang sudah ada"
                  required
                  dense
                  outlined
                >
                  <template v-slot:append>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on }">
                        <v-icon v-on="on" @click="getRandomToken">
                          mdi-dice-multiple
                        </v-icon>
                      </template>
                      Randomize Token
                    </v-tooltip>
                  </template>
                </v-text-field> -->
                <v-autocomplete
                  v-model="categories_id"
                  :items="cateData"
                  :item-text="getCourseCategorySelectText"
                  item-value="id"
                  placeholder="Select course category"
                  label="Course Category to Unlock"
                  class="input"
                  required
                  outlined
                  dense
                ></v-autocomplete>
              </v-form>
              <div class="btn-duo mt-6">
                <button
                  v-if="id == ''"
                  @click="submit()"
                  :disabled="addLoading"
                  class="btn btn-blue btn-sm"
                >
                  {{ addLoading ? "Loading..." : "Save" }}
                </button>
                <button
                  v-if="id"
                  @click="update()"
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
import DOMPurify from "dompurify";
export default {
  name: "VoucherPage",
  metaInfo() {
    return {
      title: "Voucher - AcmeLMS",
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
      storageBaseURL: process.env.VUE_APP_STORAGE_BASE_URL,
      tokenRules: [
        (v) =>
          /[A-Z0-9]{10}/.test(v) ||
          "Token harus berisi 10 karakter, hanya mengandung huruf besar + angka, dan tidak boleh sama dengan voucher yang sudah ada",
      ],
      isLoading: false,
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      id: "",
      token: "",
      categories_id: "",
      cateData: [],
      url: "",
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
          text: "Token",
          value: "token",
          width: "10%",
        },
        {
          text: "Unlock Course",
          value: "course_category_id",
          width: "20%",
        },
        {
          text: "Redeemed",
          value: "is_redeemed",
          width: "10%",
        },
        {
          text: "Reedemed By",
          value: "redeemed_by",
          width: "20%",
        },
        {
          text: "Action",
          value: "action",
          width: "20%",
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
    getRandomToken() {
      const length = 10;
      let result = "";
      const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      const charactersLength = characters.length;
      let counter = 0;
      while (counter < length) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
        counter += 1;
      }
      this.token = result;
    },
    getCourseCategorySelectText(courseCategory) {
      return `${courseCategory.name} - ${courseCategory.type}`;
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
    getMainData() {
      var search = this.search;
      var page = this.options.page;
      var itemsPerPage = this.options.itemsPerPage;
      var sortBy = this.options.sortBy[0]
        ? this.options.sortBy[0]
        : "created_at";
      var sortDesc = this.options.sortDesc[0]
        ? this.options.sortDesc[0]
        : "DESC";

      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/voucher?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.voucher.rows;
            this.totalData = res.data.data.voucher.count;
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

    openDialog(item) {
      if (!item) {
        this.id = "";
        this.token = "";
        this.categories_id = "";
      } else {
        this.id = item.id;
        this.token = item.token;
        this.categories_id = item.course_category_id;
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
    },

    submit() {
      this.addLoading = true;
      var formData = new FormData();
      this.getRandomToken();
      formData.append("token", DOMPurify.sanitize(this.token)); // Membersihkan nilai
      formData.append(
        "course_category_id",
        DOMPurify.sanitize(this.categories_id)
      ); // Membersihkan nilai
      this.$root
        .upload("post", "/v1/admin/voucher/create", formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            setTimeout(() => {
              this.dialog = false;
              this.getMainData();
              this.addLoading = false;
              this.image = null;

              this.alert(res.data.message, "success");
            }, 1000);
            return;
          } else {
            // console.log(res);
            if (res.data.message == "Error validation") {
              if (res.data.data.image) {
                this.alert(res.data.data.image[0], "danger");
              } else {
                this.alert("Semua field wajib diisi", "danger");
              }
            } else {
              this.alert(res.data.message, "danger");
            }
            this.addLoading = false;
          }
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },

    update() {
      this.addLoading = true;
      var formData = new FormData();
      formData.append("token", DOMPurify.sanitize(this.token)); // Membersihkan nilai
      formData.append(
        "course_category_id",
        DOMPurify.sanitize(this.categories_id)
      ); // Membersihkan nilai
      this.$root
        .upload("post", `/v1/admin/voucher/update/${this.id}`, formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            setTimeout(() => {
              this.dialog = false;
              this.getMainData();
              this.addLoading = false;
              this.image = null;

              this.alert(res.data.message, "success");
            }, 1000);

            return;
          } else {
            this.alert(res.data.message, "danger");
            this.addLoading = false;
          }
        })
        .catch((e) => {
          console.log(e);
          this.addLoading = false;
        });
    },

    submitDelete() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "post",
          url: `/v1/admin/voucher/delete/${this.id}`,
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
  },
  mounted() {
    this.getCategoryData();
    this.getMainData();
    this.$root.cms_tab = "default";
  },
};
</script>

<style lang="scss" scoped>
.dialog-form {
  margin-top: 3rem;
}

.dialog-form .input {
  margin-bottom: 1rem;
}
</style>
