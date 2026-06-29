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
                  <router-link to="/admin/course-category"
                    >Course Category</router-link
                  >
                  <div class="active">{{ id ? "Edit" : "Add" }}</div>
                </div>
                <h2>Course Category {{ id ? "Edit" : "Add" }}</h2>
              </div>
              <div class="mt-24 f-form">
                <div class="f-label">Banner</div>
                <img
                  :src="img_d"
                  alt=""
                  style="width: 150px; margin-bottom: 16px"
                />
                <v-file-input
                  v-model="banner"
                  show-size
                  placeholder="Select Image"
                  @change="imageOnChange_d"
                  outlined
                  dense
                ></v-file-input>
                <div class="f-label">Category Name</div>
                <v-text-field
                  v-model="name"
                  placeholder="Enter category name"
                  required
                  outlined
                  dense
                ></v-text-field>
                <div class="f-label">Category Type</div>
                <v-select
                  v-model="type"
                  :items="typeItems"
                  v-on:change="setType"
                  item-text="name"
                  item-value="value"
                  required
                  outlined
                  dense
                ></v-select>
                <div v-if="type === 'prakerja'">
                  <div class="f-label">Prakerja Course ID</div>
                  <v-text-field
                    v-model="prakerja_course_id"
                    placeholder="1185739582"
                    required
                    outlined
                    dense
                    hint="ID yang diberikan oleh prakerja untuk course ini. Harus unik untuk setiap course"
                  ></v-text-field>
                </div>
                <div class="f-label">Color</div>
                <v-autocomplete
                  v-model="color"
                  :items="colorItems"
                  placeholder="Select color"
                  required
                  outlined
                  dense
                ></v-autocomplete>
                <div class="f-label">Expert</div>
                <v-autocomplete
                  v-model="tutors_id"
                  :items="tutorData"
                  item-text="name"
                  item-value="id"
                  placeholder="Select expert"
                  required
                  multiple
                  outlined
                  dense
                ></v-autocomplete>
                <div class="f-label">Description</div>
                <div class="ck">
                  <ckeditor
                    v-model="description"
                    :config="editorConfig"
                    :editor="editor"
                  ></ckeditor>
                  <div class="ck-count">{{ description.length }}</div>
                </div>
                <div v-show="type === 'prakerja'">
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
                  <div class="f-label">Price</div>
                  <v-text-field
                    v-model="price"
                    placeholder="Enter price"
                    required
                    outlined
                    dense
                    hint="insert 0 if free course"
                  ></v-text-field>
                </div>
                <div v-show="type !== 'prakerja'">
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
                  <div class="f-label">Price</div>
                  <v-text-field
                    v-model="price"
                    placeholder="Enter price"
                    required
                    outlined
                    dense
                    hint="insert 0 if free course"
                  ></v-text-field>
                  <div v-if="price > 0">
                    <div class="f-label">Link Pembelian</div>
                    <v-text-field
                      v-model="link_payment"
                      placeholder="Enter link"
                      required
                      outlined
                      dense
                    ></v-text-field>
                  </div>
                </div>
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
      addLoading: false,
      id: "",
      name: "",
      description: "",
      what_learn: "",
      requirements: "",
      durasi_pelatihan: "",
      metode_absensi: "",
      prasyarat: "",
      type: "",
      prakerja_course_id: "",
      skill: "",
      price: "",
      link_payment: "",
      status: "draft",
      banner: null,
      img_d: "",
      color: "",
      typeItems: [
        {
          name: "MSIB",
          value: "msib",
        },
        {
          name: "Public Bootcamp",
          value: "public_bootcamp",
        },
        {
          name: "Prakerja",
          value: "prakerja",
        },
      ],
      colorItems: ["blue", "purple", "green", "orange", "red"],
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
  methods: {
    setType() {
      this.$forceUpdate();
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

    getCourseCategory(id) {
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course-category/read/${id}`,
        })
        .then((res) => {
          // console.log("DATA:", res.data.data);
          if (!res.data.error) {
            var course = res.data.data.CourseCategory;
            // console.log("Course:", course);
            this.color = course.color;
            this.tutors_id = course.tutor.map((t) => t.id);
            this.name = course.name;
            this.description = course.description;
            this.what_learn = Array.isArray(course.what_learn)
              ? course.what_learn.join("\n")
              : "";
            this.skill = Array.isArray(course.skill)
              ? course.skill.join("\n")
              : "";
            this.type = course.type;
            this.prakerja_course_id = course.prakerja_course_id;
            this.metode_absensi = Array.isArray(course.metode_absensi)
              ? course.metode_absensi.join("\n")
              : "";
            this.prasyarat = Array.isArray(course.prasyarat)
              ? course.prasyarat.join("\n")
              : "";
            this.durasi_pelatihan = course.durasi_pelatihan;
            this.price = course.price;
            this.link_payment = course.link_payment;
            this.status = course.status;
            this.img_d = this.$root.storageBaseURL + course.banner;
            return;
          }
        })
        .catch((e) => {
          console.log(e);
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
      var endpointUrl = "/v1/admin/course-category/create";
      var formData = new FormData();
      if (this.id) {
        endpointUrl = `/v1/admin/course-category/update/${this.id}`;
      }
      formData.append("color", this.color);
      formData.append("tutors_ids", DOMPurify.sanitize(this.tutors_id));
      formData.append("name", this.name);
      formData.append("description", this.description);
      formData.append(
        "what_learn",
        JSON.stringify(this.what_learn.split("\n"))
      );
      formData.append("type", this.type);
      formData.append(
        "prakerja_course_id",
        DOMPurify.sanitize(this.prakerja_course_id)
      );
      formData.append("skill", JSON.stringify(this.skill.split("\n")));
      formData.append(
        "requirements",
        this.requirements ? JSON.stringify(this.requirements.split("\n")) : ""
      );
      formData.append("price", DOMPurify.sanitize(this.price));
      formData.append("status", DOMPurify.sanitize(this.status));
      formData.append("link_payment", DOMPurify.sanitize(this.link_payment));
      formData.append("banner", this.banner === null ? "" : this.banner);
      formData.append("durasi_pelatihan", this.durasi_pelatihan);
      formData.append(
        "metode_absensi",
        JSON.stringify(this.metode_absensi.split("\n"))
      );
      formData.append("prasyarat", JSON.stringify(this.prasyarat.split("\n")));
      this.$root
        .upload("post", endpointUrl, formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            setTimeout(() => {
              this.addLoading = false;
              this.$router.push(`/admin/course-category`);
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
      this.textNotif = DOMPurify.sanitize(item); // Membersihkan nilai
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
  mounted() {
    this.getTutorData();
    var id = this.$route.params.id;
    if (id) {
      this.id = id;
      this.getCourseCategory(id);
    }
    this.$root.cms_tab = "course-category";
  },
};
</script>
