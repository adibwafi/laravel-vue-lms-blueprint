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
                <h2>Expert list</h2>
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
                        :src="`${storageBaseUrl}` + item.image"
                        alt=""
                        class="imgs"
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
            <v-card-text>
              <v-form class="mt-24">
                <div v-if="img_d">
                  <img
                    :src="img_d"
                    alt=""
                    style="width: 150px; margin-bottom: 16px"
                  />
                </div>
                <v-file-input
                  v-model="image"
                  show-size
                  label="Image"
                  @change="imageOnChange_d"
                ></v-file-input>
                <v-text-field
                  v-model="name"
                  label="Name"
                  placeholder="Enter name"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="job"
                  label="Job"
                  placeholder="Enter job"
                  required
                ></v-text-field>
                <v-text-field
                  v-model="link_profile"
                  label="Linkedin Url"
                  placeholder="Enter Linkedin Url"
                  required
                ></v-text-field>
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
  name: "ExpertPage",
  metaInfo() {
    return {
      title: "Expert - AcmeLMS",
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
      storageBaseUrl: process.env.VUE_APP_STORAGE_BASE_URL,
      addLoading: false,
      dialog: false,
      dialogDelete: false,
      id: "",
      name: "",
      image: null,
      img_d: "",
      job: "",
      link_profile: "",
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
          width: "20%",
        },
        {
          text: "Name",
          value: "name",
          width: "40%",
        },
        {
          text: "Job",
          value: "job",
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
          url: `/v1/admin/tutor?search=${search}&page=${page}&limit=${itemsPerPage}&sortBy=${sortBy}&orderBy=${sortDesc}`,
        })
        .then((res) => {
          // console.log(res);
          if (!res.data.error) {
            this.datarow = res.data.data.tutors.rows;
            this.totalData = res.data.data.tutors.count;
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
        this.link_profile = "";
        this.img_d = "";
        this.name = "";
        this.job = "";
        this.image = null;
      } else {
        this.id = item.id;
        this.link_profile = item.link_profile;
        this.name = item.name;
        this.job = item.job;
        this.img_d = `${process.env.VUE_APP_STORAGE_BASE_URL}${item.image}`;
      }
      this.dialog = true;
    },

    deleteItem(item) {
      this.selectedItem = item;
      this.dialogDelete = true;
      this.id = item.id;
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

    submit() {
      this.addLoading = true;
      var formData = new FormData();
      formData.append("name", DOMPurify.sanitize(this.name)); // Membersihkan nilai
      formData.append("job", DOMPurify.sanitize(this.job)); // Membersihkan nilai
      formData.append("image", this.image == null ? "" : this.image);
      formData.append("link_profile", DOMPurify.sanitize(this.link_profile)); // Membersihkan nilai
      this.$root
        .upload("post", "/v1/admin/tutor/create", formData)
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
      formData.append("name", DOMPurify.sanitize(this.name)); // Membersihkan nilai
      formData.append("job", DOMPurify.sanitize(this.job)); // Membersihkan nilai
      formData.append("image", this.image == null ? "" : this.image);
      formData.append("link_profile", DOMPurify.sanitize(this.link_profile)); // Membersihkan nilai
      this.$root
        .upload("post", `/v1/admin/tutor/update/${this.id}`, formData)
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
          url: `/v1/admin/tutor/delete/${this.id}`,
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
