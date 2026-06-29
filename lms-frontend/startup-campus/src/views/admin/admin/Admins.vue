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
                <h2>Admin list</h2>
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
                  <template v-slot:[`item.image`]="{ item }">
                    <div class="dataimg">
                      <img
                        v-if="item.image"
                        :src="`${storageBaseUrl}` + item.image"
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
                      <span>Edit Admin</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="pink"
                          class="mrb-8"
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
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-if="item.is_blocked == '0'"
                          class="mrb-8"
                          color="red"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="blockUser(item)"
                        >
                          <v-icon dark> mdi-account-cancel </v-icon>
                        </v-btn>
                      </template>
                      <span>Block User</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          v-if="item.is_blocked == '1'"
                          class="mrb-8"
                          color="teal"
                          fab
                          dark
                          x-small
                          v-bind="attrs"
                          v-on="on"
                          @click="unblockUser(item)"
                        >
                          <v-icon dark> mdi-account-lock-open </v-icon>
                        </v-btn>
                      </template>
                      <span>Unblock User</span>
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
                >Apakah kamu yakin untuk menghapus user {{ fullname }}?
              </v-toolbar>
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
        <!-- Modal Block / Unblock item -->
        <v-dialog
          v-model="dialogBlock"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogBlock">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to block
                {{
                  selectedItem.fullname
                    ? '"' + selectedItem.fullname + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitBlock()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Block" }}
                  </button>
                  <a
                    @click="dialogBlock.value = false"
                    class="btn btn-text btn-sm tf"
                    >Cancel</a
                  >
                </div>
              </v-card-text>
            </v-card>
          </template>
        </v-dialog>
        <v-dialog
          v-model="dialogUnblock"
          transition="dialog-bottom-transition"
          max-width="600"
        >
          <template v-slot:default="dialogUnblock">
            <v-card>
              <v-toolbar color="#09408e" dark
                >Are you sure to unblock
                {{
                  selectedItem.fullname
                    ? '"' + selectedItem.fullname + '"'
                    : "this item"
                }}
                ?</v-toolbar
              >
              <v-card-text>
                <div class="btn-duo mt-6">
                  <button
                    @click="submitUnblock()"
                    :disabled="isLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ isLoading ? "Loading..." : "Unblock" }}
                  </button>
                  <a
                    @click="dialogUnblock.value = false"
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
              {{ id ? "Edit Item" : "Add New" }}</v-toolbar
            >
            <v-card-text>
              <v-form class="mt-24">
                <img
                  :src="img_d"
                  alt=""
                  style="width: 150px; margin-bottom: 16px"
                />
                <v-file-input
                  v-model="image"
                  show-size
                  placeholder="Select Image"
                  @change="imageOnChange_d"
                  outlined
                  dense
                ></v-file-input>
                <v-text-field
                  v-model="fullname"
                  label="Nama"
                  placeholder="Agus Pramudyo"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="email"
                  label="Email"
                  placeholder="aguspramudyo@gmail.com"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="password"
                  label="Password"
                  placeholder="*********"
                  :disabled="id ? true : false"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="phone"
                  label="Nomor Telfon"
                  placeholder="081390494123"
                  required
                ></v-text-field>
              </v-form>
              <div class="btn-duo mt-6">
                <button
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
import DOMPurify from "dompurify";
export default {
  name: "AdminPage",
  metaInfo() {
    return {
      title: "Admin - AcmeLMS",
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
      dialogBlock: false,
      dialogUnblock: false,
      dialogDelete: false,
      id: "",
      fullname: "",
      email: "",
      password: "",
      phone: "",
      image: undefined,
      img_d: "",
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
          value: "image",
          width: "10%",
        },
        {
          text: "Fullname",
          value: "fullname",
          width: "25%",
        },
        {
          text: "Email",
          value: "email",
          width: "30%",
        },
        {
          text: "Phone",
          value: "phone",
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
    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
      this.fullname = item.fullname;
    },
    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
    },
    openDialog(item) {
      if (!item) {
        this.id = "";
        this.fullname = "";
        this.password = "";
        this.email = "";
        this.phone = "";
        this.image = undefined;
        this.img_d = "";
      } else {
        this.id = item.id;
        this.fullname = item.fullname;
        this.email = item.email;
        this.password = item.password;
        this.phone = item.phone;
        this.img_d = this.$root.storageBaseURL + item.image;
        this.image = undefined;
      }
      this.dialog = true;
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
          url: `/v1/admin/user?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}&admin_only=true`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.user.rows;
            this.totalData = res.data.data.user.count;
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

    seeCourses(item) {
      this.$router.push({
        path: `/admin/users/${item.id}/courses`,
      });
    },

    blockUser(item) {
      this.selectedItem = item;
      this.dialogBlock = true;
      this.id = item.id;
    },

    unblockUser(item) {
      this.selectedItem = item;
      this.dialogUnblock = true;
      this.id = item.id;
    },

    submit() {
      this.addLoading = true;
      const formData = new FormData();
      if (this.id) {
        formData.append("id", this.id);
      }
      formData.append("fullname", DOMPurify.sanitize(this.fullname)); // Membersihkan nilai
      formData.append("email", DOMPurify.sanitize(this.email)); // Membersihkan nilai
      formData.append("password", DOMPurify.sanitize(this.password)); // Membersihkan nilai
      formData.append("phone", DOMPurify.sanitize(this.phone)); // Membersihkan nilai
      if (this.image) {
        formData.append("image", this.image);
      }

      const endpoint = this.id
        ? `/v1/admin/user/update/${this.id}`
        : "/v1/admin/user/create/admin";

      this.$root
        .upload("post", endpoint, formData)
        .then((res) => {
          // console.log("🚀 ~ file: Admins.vue:426 ~ .then ~ res", res);
          if (res.data.success) {
            setTimeout(() => {
              this.dialog = false;
              this.getMainData();
              this.addLoading = false;

              this.alert(res.data.message, "success");
            }, 1000);
          } else {
            if (res.data.message == "Error validation") {
              this.alert("Semua field wajib diisi", "danger");
            } else {
              this.alert(DOMPurify.sanitize(res.data.message), "danger"); // Membersihkan nilai
            }
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
          url: `/v1/admin/user/delete/${this.id}`,
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

    submitBlock() {
      var vm = this;
      vm.isLoading = true;
      var data = {
        is_blocked: 1,
      };
      vm.$root
        .axios({
          method: "post",
          url: `/v1/admin/user/is-blocked/${vm.id}`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            vm.alert(res.data.message, "success");
            vm.getMainData();
            this.$router.go(0);
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          vm.dialogBlock = false;
          vm.isLoading = false;
        });
    },

    submitUnblock() {
      var vm = this;
      vm.isLoading = true;
      var data = {
        is_blocked: 0,
      };
      vm.$root
        .axios({
          method: "post",
          url: `/v1/admin/user/is-blocked/${vm.id}`,
          data: data,
        })
        .then((res) => {
          if (res.data.success) {
            vm.alert(res.data.message, "success");
            vm.getMainData();
            this.$router.go(0);
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          vm.dialogUnblock = false;
          vm.isLoading = false;
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
    this.getMainData();
    this.$root.cms_tab = "default";
  },
};
</script>
