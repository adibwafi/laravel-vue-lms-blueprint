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
              <div class="k-head">
                <div class="breadcrumbs">
                  <router-link to="/admin/course">Course</router-link>
                  <div class="active">{{ id ? "Edit" : "Add" }}</div>
                </div>
                <h2>Course {{ id ? "Edit" : "Add" }}</h2>
              </div>
              <div class="mt-24 f-form">
                <div class="f-label">Banner</div>
                <div v-if="img_d">
                  <img
                    :src="img_d"
                    alt=""
                    style="width: 150px; margin-bottom: 16px"
                  />
                </div>
                <v-file-input
                  v-model="banner"
                  show-size
                  placeholder="Select Image"
                  @change="imageOnChange_d"
                  outlined
                  dense
                ></v-file-input>
                <div class="f-label">Course Category</div>
                <v-autocomplete
                  v-model="categories_id"
                  :items="cateData"
                  :item-text="getCourseCategorySelectText"
                  item-value="id"
                  placeholder="Select course category"
                  required
                  outlined
                  dense
                ></v-autocomplete>
                <div class="f-label">Expert</div>
                <v-autocomplete
                  v-model="tutors_id"
                  :items="tutorData"
                  :item-text="getTutorSelectText"
                  item-value="id"
                  placeholder="Select expert"
                  required
                  chips
                  clearable
                  deletable-chips
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
                <div class="f-label">Course Name</div>
                <v-text-field
                  v-model="name"
                  placeholder="Enter course name"
                  required
                  outlined
                  dense
                ></v-text-field>
                <div class="f-label">Order</div>
                <v-text-field
                  v-model="order"
                  :rules="orderRules"
                  required
                  outlined
                  dense
                  hint="Nilai bisa 1 sampai 1001, course dengan order yang rendah akan muncul paling atas"
                  type="number"
                />
                <div class="f-label">Description</div>
                <div class="ck">
                  <ckeditor
                    v-model="description"
                    :editor="editor"
                    :config="editorConfig"
                  ></ckeditor>
                  <div class="ck-count">{{ description.length }}</div>
                </div>
                <div v-show="courseCategoryType === 'prakerja'">
                  <div class="f-label">Prasyarat & Kriteria Pelatihan</div>
                  <v-textarea
                    v-model="prasyarat"
                    placeholder="Tambahkan informasi tentang prasyarat dan kriteria pelatihan"
                    hint="press enter for a new list"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <div class="f-label">Metode Absensi</div>
                  <v-textarea
                    v-model="metode_absensi"
                    placeholder="Tambahkan informasi tentang metode absensi"
                    hint="press enter for a new list"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <div class="f-label">Durasi Program Pelatihan</div>
                  <v-textarea
                    v-model="durasi_pelatihan"
                    placeholder="Tambahkan informasi tentang durasi pelatihan"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <v-checkbox
                    color="primary"
                    v-model="is_unjuk_keterampilan"
                    @click="
                      is_unjuk_keterampilan =
                        is_unjuk_keterampilan === 1 ? 0 : 1
                    "
                    :label="`Ini adalah Unjuk Keterampilan`"
                    class="check"
                  ></v-checkbox>
                </div>
                <div v-show="courseCategoryType !== 'prakerja'">
                  <div class="f-label">What Learn</div>
                  <v-textarea
                    v-model="what_learn"
                    placeholder="Enter what learn"
                    hint="press enter for a new list"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <div class="f-label">Skill</div>
                  <v-textarea
                    v-model="skill"
                    placeholder="Enter skill"
                    hint="press enter for a new list"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <div class="f-label">Requirements (optional)</div>
                  <v-textarea
                    v-model="requirements"
                    placeholder="Enter requirements"
                    hint="press enter for a new list"
                    required
                    outlined
                    dense
                  ></v-textarea>
                  <div class="f-label">Course Type</div>
                  <v-autocomplete
                    v-model="isPaid"
                    :items="paidItem"
                    item-text="name"
                    item-value="id"
                    placeholder="Select course type"
                    required
                    outlined
                    dense
                  ></v-autocomplete>
                </div>
                <v-text-field
                  v-model="link_zoom"
                  label="Link Zoom (Opsional)"
                  placeholder="Enter link zoom"
                  hint="Kosongkan jika tidak menggunakan link zoom"
                  required
                ></v-text-field>
                <div class="btn-duo mt-6">
                  <button
                    @click="submit()"
                    :disabled="addLoading"
                    class="btn btn-blue btn-sm"
                  >
                    {{ addLoading ? "Loading..." : "Save" }}
                  </button>
                  <a @click="$router.go(-1)" class="btn btn-text btn-sm tf"
                    >Cancel</a
                  >
                </div>
              </div>
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
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
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
      addLoading: false,
      id: "",
      usingZoom: "",
      link_zoom: "",
      name: "",
      description: "",
      what_learn: "",
      requirements: "",
      skill: "",
      isPaid: "",
      metode_absensi: "",
      prasyarat: "",
      durasi_pelatihan: "",
      is_unjuk_keterampilan: false,
      order: 0,
      orderRules: [
        (v) => !!v || "Order is required",
        (v) => (v >= 1 && v <= 1001) || "Order must be between 1 and 1001",
      ],
      paidItem: [
        {
          id: 1,
          name: "Berbayar",
        },
        {
          id: 0,
          name: "Gratis",
        },
      ],
      status: "draft",
      banner: null,
      img_d: "",
      categories_id: "",
      tutors_id: "",

      cateData: [],
      tutorData: [],
      editor: ClassicEditor,
      editorConfig: {
        toolbar: [
          "Bold",
          "Italic",
          "|",
          "NumberedList",
          "BulletedList",
          "|",
          "Link",
          "|",
          "Blockquote",
          "|",
          "Undo",
          "Redo",
          "|",
        ],
      },

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,
    };
  },
  computed: {
    courseCategoryType() {
      const courseCategory = this.cateData.find(
        (item) => item.id === this.categories_id
      );
      if (courseCategory) {
        return courseCategory.type;
      }
      return undefined;
    },
  },
  methods: {
    getCourseCategorySelectText(courseCategory) {
      return `${courseCategory.name} - ${courseCategory.type}`;
    },
    getTutorSelectText(tutor) {
      return `${tutor.name} - ${tutor.job}`;
    },
    getTutorData() {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/tutor?page=1&limit=9999999&sortBy=name&order=asc`,
        })
        .then((res) => {
          if (!res.data.error) {
            this.tutorData = res.data.data.tutors.rows;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
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

    getCourse(id) {
      this.actLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course/read/${id}`,
        })
        .then((res) => {
          if (!res.data.error) {
            var course = res.data.data.Course;
            // console.log(course);
            this.categories_id = course.categories_id;
            this.tutors_id = course.tutor.map((t) => t.id);
            this.name = course.name;
            this.description = course.description;
            this.what_learn = Array.isArray(course.what_learn)
              ? course.what_learn.join("\n")
              : "";
            this.skill = Array.isArray(course.skill)
              ? course.skill.join("\n")
              : "";
            this.order = course.order;
            this.requirements = Array.isArray(course.requirements)
              ? course.requirements.join("\n")
              : "";
            this.isPaid = course.isPaid;
            this.status = course.status;
            this.metode_absensi = Array.isArray(course.metode_absensi)
              ? course.metode_absensi.join("\n")
              : "";
            this.prasyarat = Array.isArray(course.prasyarat)
              ? course.prasyarat.join("\n")
              : "";
            this.durasi_pelatihan = course.durasi_pelatihan;
            this.is_unjuk_keterampilan = course.is_unjuk_keterampilan == 1;
            this.img_d = this.$root.storageBaseURL + course.banner;
            this.link_zoom = course.link_zoom;
            return;
          }
        })
        .catch((e) => {
          console.error(e);
          this.alert("Error fetching course data", "danger");
        })
        .finally(() => {
          this.actLoading = false;
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
      var endpointUrl = "/v1/admin/course/create";
      var formData = new FormData();
      if (this.id) {
        endpointUrl = `/v1/admin/course/update/${this.id}`;
      }
      formData.append("categories_id", this.categories_id);
      formData.append("tutors_ids", this.tutors_id);
      formData.append("name", this.name);
      formData.append("description", this.description);
      formData.append("durasi_pelatihan", this.durasi_pelatihan);
      formData.append(
        "metode_absensi",
        JSON.stringify(this.metode_absensi.split("\n"))
      );

      formData.append("prasyarat", JSON.stringify(this.prasyarat.split("\n")));
      formData.append(
        "what_learn",
        JSON.stringify(this.what_learn.split("\n"))
      );
      formData.append("skill", JSON.stringify(this.skill.split("\n")));
      formData.append(
        "requirements",
        this.requirements ? JSON.stringify(this.requirements.split("\n")) : ""
      );
      formData.append("isPaid", this.isPaid);
      formData.append("order", this.order);
      formData.append("status", this.status);
      formData.append("banner", this.banner === null ? "" : this.banner);
      formData.append("link_zoom", this.link_zoom);
      formData.append(
        "is_unjuk_keterampilan",
        this.is_unjuk_keterampilan ? 1 : 0
      );
      this.$root
        .upload("post", endpointUrl, formData)
        .then((res) => {
          // console.log(res.data.data, "<><><><><><><>");
          if (res.data.success) {
            this.alert(res.data.message, "success");
            setTimeout(() => {
              this.addLoading = false;
              this.$router.push(`/admin/course`);
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
    this.getCategoryData();
    this.getTutorData();
    var id = this.$route.params.id;
    if (id) {
      this.id = id;
      this.getCourse(id);
    }

    this.$root.cms_tab = "course";
  },
};
</script>
