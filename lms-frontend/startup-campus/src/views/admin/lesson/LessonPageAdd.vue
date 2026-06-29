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
                  <router-link to="/admin/course">{{
                    dataParent?.course?.name
                  }}</router-link>
                  <router-link :to="`/admin/course/${this.course_id}/lesson`">{{
                    dataParent?.name
                  }}</router-link>
                  <router-link
                    :to="`/admin/course/${this.course_id}/lesson/${this.lesson_id}/page`"
                  >
                    {{ lesson_page_id ? name : "Lesson Page" }}
                  </router-link>
                  <div class="active">
                    {{ lesson_page_id ? "Edit" : "Add" }}
                  </div>
                </div>
                <h2>
                  Lesson Page
                  {{ lesson_page_id ? "Edit" : "Add" }}
                </h2>
              </div>
              <div class="mt-24 f-form">
                <div class="f-label">Media Type</div>
                <v-autocomplete
                  v-model="media_type"
                  :items="mediatypeList"
                  item-text="name"
                  item-value="value"
                  placeholder="Select media type"
                  required
                  outlined
                  dense
                ></v-autocomplete>
                <div v-if="media_type == 'image'">
                  <div class="f-label">Image</div>
                  <div v-if="img_d">
                    <img
                      :src="img_d"
                      alt=""
                      style="width: 150px; margin-bottom: 16px"
                    />
                  </div>
                  <v-file-input
                    v-model="media_link"
                    show-size
                    placeholder="Select File"
                    @change="imageOnChange_d"
                    accept="image/*"
                    outlined
                    dense
                  ></v-file-input>
                </div>
                <div v-if="media_type == 'pdf'">
                  <div class="f-label">PDF</div>
                  <div
                    v-if="pdf_d"
                    :style="{
                      maxWidth: '414px',
                      border: '1px solid #ccc',
                    }"
                  >
                    <div
                      :style="{
                        padding: '16px',
                      }"
                    >
                      <h2>Preview PDF</h2>
                    </div>
                    <PdfViewer
                      :source="pdf_d"
                      :page="1"
                      :width="414"
                      :scale="1"
                    />
                  </div>
                  <v-file-input
                    v-model="media_link"
                    show-size
                    placeholder="Select File"
                    @change="pdfOnChange_d"
                    accept="application/pdf"
                    outlined
                    dense
                  ></v-file-input>
                </div>
                <div v-if="media_type == 'video'">
                  <div v-if="media_link">
                    <iframe
                      width="560"
                      height="315"
                      :src="`https://www.youtube.com/embed/` + media_link"
                      frameborder="0"
                      allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
                  </div>
                  <div class="f-label">Video Url</div>
                  <v-text-field
                    v-model="media_link"
                    placeholder="Enter Youtube Id"
                    required
                    outlined
                    dense
                  ></v-text-field>
                </div>
                <div class="f-label">Name</div>
                <v-text-field
                  v-model="name"
                  placeholder="Enter lesson page name"
                  required
                  outlined
                  dense
                ></v-text-field>
                <div class="f-label">Content</div>
                <div class="content-area">
                  <div class="ck">
                    <ckeditor
                      v-model="content"
                      :config="editorConfig"
                      :editor="editor"
                    ></ckeditor>
                    <div class="ck-count">{{ content.length }}</div>
                  </div>
                  <div class="preview">
                    <h4 class="header">Preview Content</h4>
                    <ContentSection :content="content" />
                  </div>
                </div>
                <div class="f-label">
                  Tooltips
                  <a @click="addTooltips" class="btn btn-blue btn-xs ml-2"
                    >Add</a
                  >
                  <a
                    :href="require('@/assets/img/guide-tooltips.jpg')"
                    target="_blank"
                    style="margin-left: 10px"
                    ><v-icon small color="black"
                      >mdi-help-box-multiple</v-icon
                    ></a
                  >
                  <v-row
                    class="mt-2"
                    v-for="(item, index) in tooltips"
                    :key="index"
                  >
                    <v-col cols="2">
                      <v-text-field
                        v-model="item.key"
                        placeholder="Text Key"
                        required
                        outlined
                        dense
                        :hint="`Contoh: ${keyTooltip}`"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="2">
                      <v-text-field
                        v-model="item.value"
                        placeholder="Text Display"
                        required
                        outlined
                        dense
                        hint="Contoh: My Hero"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="7">
                      <v-text-field
                        v-model="item.message"
                        placeholder="Enter tooltips message"
                        required
                        outlined
                        dense
                        hint="Contoh: A person who is admired or idealized ..."
                      ></v-text-field>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            color="pink"
                            fab
                            dark
                            x-small
                            v-bind="attrs"
                            v-on="on"
                            @click="removeTooltips(index)"
                          >
                            <v-icon dark> mdi-delete </v-icon>
                          </v-btn>
                        </template>
                        <span>Remove Tooltips</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </div>
                <div class="f-label">Content Type</div>
                <v-autocomplete
                  v-model="content_type"
                  :items="contentList"
                  item-text="name"
                  item-value="value"
                  placeholder="Select content type"
                  required
                  outlined
                  dense
                ></v-autocomplete>
                <div v-if="content_type == 'multiple_choice'">
                  <v-row class="answer-box">
                    <v-col cols="7">
                      <div class="f-label">Answer 1</div>
                      <v-text-field
                        v-model="answer1"
                        placeholder="Enter answer 1"
                        required
                        outlined
                        dense
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="5">
                      <div class="mt-9">
                        <v-switch v-model="switch1"></v-switch>
                      </div>
                    </v-col>
                    <v-col cols="8" :style="{ paddingTop: 0 }">
                      <div class="f-label">
                        Text Popup 1 (Opsional)
                        <a
                          href="https://docs.google.com/document/d/1nxw3U1KoyHLWOltdPYOcnyO08uqtHUgFdDvMgULJNrw/edit?usp=sharing"
                          target="_blank"
                          style="margin-left: 10px"
                          ><v-icon small color="black"
                            >mdi-help-box-multiple</v-icon
                          ></a
                        >
                      </div>
                      <v-text-field
                        v-model="textPopup1"
                        placeholder="Enter text popup 1"
                        outlined
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row class="answer-box">
                    <v-col cols="7">
                      <div class="f-label">Answer 2</div>
                      <v-text-field
                        v-model="answer2"
                        placeholder="Enter answer 2"
                        required
                        outlined
                        dense
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="5">
                      <div class="mt-9">
                        <v-switch v-model="switch2"></v-switch>
                      </div>
                    </v-col>
                    <v-col cols="8" :style="{ paddingTop: 0 }">
                      <div class="f-label">
                        Text Popup 2 (Opsional)
                        <a
                          href="https://docs.google.com/document/d/1nxw3U1KoyHLWOltdPYOcnyO08uqtHUgFdDvMgULJNrw/edit?usp=sharing"
                          target="_blank"
                          style="margin-left: 10px"
                          ><v-icon small color="black"
                            >mdi-help-box-multiple</v-icon
                          ></a
                        >
                      </div>
                      <v-text-field
                        v-model="textPopup2"
                        placeholder="Enter text popup 2"
                        outlined
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row class="answer-box">
                    <v-col cols="7">
                      <div class="f-label">Answer 3</div>
                      <v-text-field
                        v-model="answer3"
                        placeholder="Enter answer 3"
                        required
                        outlined
                        dense
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="5">
                      <div class="mt-9">
                        <v-switch v-model="switch3"></v-switch>
                      </div>
                    </v-col>
                    <v-col cols="8" :style="{ paddingTop: 0 }">
                      <div class="f-label">
                        Text Popup 3 (Opsional)
                        <a
                          href="https://docs.google.com/document/d/1nxw3U1KoyHLWOltdPYOcnyO08uqtHUgFdDvMgULJNrw/edit?usp=sharing"
                          target="_blank"
                          style="margin-left: 10px"
                          ><v-icon small color="black"
                            >mdi-help-box-multiple</v-icon
                          ></a
                        >
                      </div>
                      <v-text-field
                        v-model="textPopup3"
                        placeholder="Enter text popup 3"
                        outlined
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row class="answer-box">
                    <v-col cols="7">
                      <div class="f-label">Answer 4</div>
                      <v-text-field
                        v-model="answer4"
                        placeholder="Enter answer 4"
                        required
                        outlined
                        dense
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="5">
                      <div class="mt-9">
                        <v-switch v-model="switch4"></v-switch>
                      </div>
                    </v-col>
                    <v-col cols="8" :style="{ paddingTop: 0 }">
                      <div class="f-label">
                        Text Popup 4 (Opsional)
                        <a
                          href="https://docs.google.com/document/d/1nxw3U1KoyHLWOltdPYOcnyO08uqtHUgFdDvMgULJNrw/edit?usp=sharing"
                          target="_blank"
                          style="margin-left: 10px"
                          ><v-icon small color="black"
                            >mdi-help-box-multiple</v-icon
                          ></a
                        >
                      </div>
                      <v-text-field
                        v-model="textPopup4"
                        placeholder="Enter text popup 4"
                        outlined
                        counter
                        maxlength="250"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </div>
                <div v-if="content_type == 'answer'">
                  <div class="f-label">Answer</div>
                  <v-textarea
                    v-model="keyword"
                    placeholder="Enter answer"
                    required
                    outlined
                    dense
                    counter
                    maxlength="50"
                  ></v-textarea>
                </div>
                <div v-if="content_type == 'fill_blank'">
                  <div class="f-label">
                    Fill in the Blank Answer
                    <a
                      @click="addAnswer"
                      class="btn btn-blue btn-xs ml-2"
                      v-if="answer.length < 5"
                      >Add</a
                    >
                    <a
                      :href="require('@/assets/img/guide-fill-blank.jpg')"
                      target="_blank"
                      style="margin-left: 10px"
                      ><v-icon small color="black"
                        >mdi-help-box-multiple</v-icon
                      ></a
                    >
                    <v-row
                      class="mt-2"
                      v-for="(item, index) in answer"
                      :key="index"
                    >
                      <v-col cols="4">
                        <v-text-field
                          v-model="item.key"
                          placeholder="Enter text key"
                          required
                          outlined
                          dense
                          :hint="`Contoh: ${keyFill}`"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="7">
                        <v-text-field
                          v-model="item.value"
                          placeholder="Enter value"
                          required
                          outlined
                          dense
                          counter
                          maxlength="100"
                          hint="Contoh: Reassuring as"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="1">
                        <v-tooltip bottom>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              color="pink"
                              fab
                              dark
                              x-small
                              v-bind="attrs"
                              v-on="on"
                              @click="removeAnswer(index)"
                            >
                              <v-icon dark> mdi-delete </v-icon>
                            </v-btn>
                          </template>
                          <span>Remove Answer</span>
                        </v-tooltip>
                      </v-col>
                    </v-row>
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
import ContentSection from "@/components/ContentSection.vue";
import PdfViewer from "@/components/PdfViewer.vue";

// import { CKFinder } from "@ckeditor/ckeditor5-ckfinder";
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
    ContentSection,
    PdfViewer,
  },
  data() {
    return {
      addLoading: false,
      img_d: "",
      pdf_d: "",

      name: "",
      course_id: "",
      lesson_id: "",
      lesson_page_id: "",
      content_type: "",
      media_link: null,
      media_type: "",
      content: "",
      keyword: "",
      contentList: [
        {
          name: "Content",
          value: "content",
        },
        {
          name: "Multiple Choice",
          value: "multiple_choice",
        },
        {
          name: "Fill in the Blank",
          value: "fill_blank",
        },
        {
          name: "Answer",
          value: "answer",
        },
      ],
      mediatypeList: [
        {
          name: "No Media",
          value: "null",
        },
        {
          name: "Image",
          value: "image",
        },
        {
          name: "Video",
          value: "video",
        },
        {
          name: "PDF",
          value: "pdf",
        },
      ],

      // multiple_choice
      answer1: "",
      switch1: true,
      textPopup1: "",

      answer2: "",
      switch2: false,
      textPopup2: "",

      answer3: "",
      switch3: false,
      textPopup3: "",

      answer4: "",
      switch4: false,
      textPopup4: "",

      // fill_blank
      answer: [],

      tooltips: [],
      editor: ClassicEditor,
      editorConfig: {
        extraPlugins: ["CKFinder", "Image"],
        ckfinder: {
          // Upload the images to the server using the CKFinder QuickUpload command.
          uploadUrl: `${process.env.VUE_APP_API_BASE_URL}/v1/ckeditor`,
        },
      },

      dataParent: {},

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
      actLoading: false,

      // Example
      keyTooltip: "{{hero}}",
      keyFill: "{_text_}",
    };
  },
  watch: {
    media_type(newMediaType) {
      if (newMediaType === "image" || newMediaType === "pdf") {
        this.media_link = null;
      }
    },
  },
  methods: {
    addTooltips() {
      this.tooltips.push({
        key: "",
        value: "",
        message: "",
      });
    },
    removeTooltips(idx) {
      this.tooltips.splice(idx, 1);
    },
    addAnswer() {
      this.answer.push({
        key: "",
        value: "",
      });
    },
    removeAnswer(idx) {
      this.answer.splice(idx, 1);
    },
    pdfOnChange_d(event) {
      if (event) {
        this.pdf_d = URL.createObjectURL(event);
      } else {
        this.pdf_d = "";
      }
    },

    imageOnChange_d(event) {
      if (event) {
        this.img_d = URL.createObjectURL(event);
      } else {
        this.img_d = "";
      }
    },

    // changeMedia(el) {
    //   var size = parseFloat(el.size / 1024);
    //   var type = el.type.split("/")[0];

    //   if (size > 1024 && type == "image") {
    //     this.alert("Image file size too big", "danger");
    //     el.target.value = null;
    //     return;
    //   }

    //   if (size > 1024 * 8 && type == "video") {
    //     this.alert("Video file size too big", "danger");
    //     el.target.value = null;
    //     return;
    //   }

    //   this.previewMedia = URL.createObjectURL(el);
    //   this.media_type = type;
    // },

    getLessonPage(id) {
      var storageBaseURL = this.$root.storageBaseURL;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/lesson-page/read/${id}`,
        })
        .then((res) => {
          if (!res.data.error) {
            var lessonPage = res.data.data.LessonPage;
            var data = JSON.parse(lessonPage.data);
            this.name = lessonPage.name;
            this.content_type = lessonPage.content_type;
            this.media_type = lessonPage.media_type;
            this.media_link = lessonPage.media_link;
            if (this.media_type === "image") {
              this.media_link = null;
              this.img_d = storageBaseURL + lessonPage.media_link;
              // this.img_d = lessonPage.media_link;
            }

            if (this.media_type === "pdf") {
              this.media_link = null;
              this.pdf_d = storageBaseURL + lessonPage.media_link;
            }

            this.tooltips = JSON.parse(lessonPage.tooltips);
            if (this.content_type === "multiple_choice") {
              data.choices.forEach((choice, i) => {
                if (i == 0) {
                  this.answer1 = choice.answer;
                  this.switch1 = choice.checked;
                  this.textPopup1 = choice.textPopup;
                } else if (i == 1) {
                  this.answer2 = choice.answer;
                  this.switch2 = choice.checked;
                  this.textPopup2 = choice.textPopup;
                } else if (i == 2) {
                  this.answer3 = choice.answer;
                  this.switch3 = choice.checked;
                  this.textPopup3 = choice.textPopup;
                } else if (i == 3) {
                  this.answer4 = choice.answer;
                  this.switch4 = choice.checked;
                  this.textPopup4 = choice.textPopup;
                }
              });
            }
            if (this.content_type === "fill_blank") {
              this.answer = data.answer;
            }
            if (this.content_type === "answer") {
              this.keyword = data.keyword;
            }
            setTimeout(() => {
              this.content = data.content;
            }, 500);
            return;
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    submit() {
      this.addLoading = true;
      var endpointUrl = "/v1/admin/lesson-page/create";
      if (this.lesson_page_id) {
        endpointUrl = `/v1/admin/lesson-page/update/${this.lesson_page_id}`;
      }
      var formData = new FormData();
      var data = {
        content: this.content,
      };

      if (this.content_type === "multiple_choice") {
        if (!this.answer1 || !this.answer2) {
          this.alert("Answer 1 and Answer 2 are required.", "danger");
          this.addLoading = false;
          return;
        }
        var choices = [
          {
            answer: this.answer1,
            checked: this.switch1,
            textPopup: this.textPopup1,
          },
          {
            answer: this.answer2,
            checked: this.switch2,
            textPopup: this.textPopup2,
          },
        ];
        if (this.answer3) {
          choices.push({
            answer: this.answer3,
            checked: this.switch3,
            textPopup: this.textPopup3,
          });
        }
        if (this.answer4) {
          choices.push({
            answer: this.answer4,
            checked: this.switch4,
            textPopup: this.textPopup4,
          });
        }
        data.choices = choices;
      }
      if (this.content_type === "fill_blank") {
        data.answer = this.answer;
      }
      if (this.content_type === "answer") {
        data.keyword = this.keyword;
      }

      formData.append("lessons_id", this.lesson_id);
      formData.append("name", this.name);
      formData.append("content_type", this.content_type);
      formData.append("data", JSON.stringify(data));
      formData.append("media_type", this.media_type);
      if (this.media_type !== "null") {
        if (this.media_type === "image") {
          formData.append("media_link", this.media_link ? this.media_link : "");
        }
        if (this.media_type === "pdf") {
          formData.append("media_link", this.media_link ? this.media_link : "");
        }
        if (this.media_type === "video") {
          formData.append("media_link", this.media_link);
        }
      } else {
        formData.append("media_link", "null");
      }
      formData.append("tooltips", JSON.stringify(this.tooltips));
      console.log(this.content_type);
      this.$root
        .upload("post", endpointUrl, formData)
        .then((res) => {
          // console.log(res);
          if (res.data.success) {
            this.alert(res.data.message, "success");
            setTimeout(() => {
              this.addLoading = false;
              this.$router.push(
                `/admin/course/${this.course_id}/lesson/${this.lesson_id}/page`
              );
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
    var course_id = this.$route.params.course_id;
    var lesson_id = this.$route.params.lesson_id;
    var lesson_page_id = this.$route.params.lesson_page_id;
    if (course_id) {
      this.course_id = course_id;
    }
    // console.log(
    //   "🚀 ~ file: LessonPageAdd.vue:818 ~ mounted ~ lesson_id:",
    //   lesson_id
    // );
    if (lesson_id) {
      this.lesson_id = lesson_id;
      this.getParentData(this.lesson_id);
    }
    if (lesson_page_id) {
      this.lesson_page_id = lesson_page_id;
      this.getLessonPage(this.lesson_page_id);
    }
    this.$root.cms_tab = "course";
  },
};
</script>

<style lang="scss" scoped>
.mt33 {
  margin-top: 33px;
}

.answer-box {
  border: 1px solid #e0e0e0;
  margin-bottom: 8px;
}

.content-area {
  margin-bottom: 54px;
  display: flex;
  flex-direction: row;
  gap: 16px;

  .ck {
    width: calc(100% - 414px - 16px);

    .ck-count {
      text-align: right;
      margin-top: 8px;
      font-size: 12px;
      color: #9e9e9e;
    }
  }

  .preview {
    width: 414px;
    border: 1px solid #e0e0e0;
    padding: 16px;
    border-radius: 4px;

    .header {
      margin-bottom: 16px;
      border-bottom: #e0e0e0 1px solid;
    }
  }
}
</style>
