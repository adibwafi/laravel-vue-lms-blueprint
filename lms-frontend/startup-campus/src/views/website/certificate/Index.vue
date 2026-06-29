<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="webapp">
    <div class="webapp-wrapper">
      <div class="alert-notif" :class="{ open: alertOpen }">
        {{ alert }}
      </div>
      <Loading :isLoading="isLoading" />

      <div v-if="isReady">
        <div class="cert-area" v-if="show == '1'">
          <div class="section">
            <a @click="$router.go(-1)">
              <div id="back-icon">
                <img src="@/assets/img/icn-back.png" alt="" />
              </div>
            </a>
            <div class="brand">
              <img src="@/assets/img/logo-white.webp" />
            </div>
            <div class="head">
              Yey Selamat! <br />Kamu telah menyelesaikan program, <br />ini
              sertifikat kamu.
            </div>
          </div>
          <div v-if="data.course_category.type == 'prakerja'">
            <div class="prakerja-cert">
              <div v-if="prakerja_course_type == 'da'">
                <img src="@/assets/cert/Sertifikat Prakerja SC_DA_1.png" />
              </div>
              <div v-else>
                <div class="cert-card-prakerja">
                  <div class="content">
                    <div
                      style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                      "
                    >
                      <div class="txt">
                        {{ data.course_category.name }}<br /><span
                          >Diberikan kepada</span
                        >
                      </div>
                      <div class="fullname">{{ data.user.fullname }}</div>
                      <div class="subtitle">
                        karena telah menyelesaikan pelatihan selama 15 jam
                      </div>
                      <div class="foot">
                        <div class="tutors">
                          <div class="user">
                            <img
                              src="@/assets/img/ttd-maryati.png"
                              class="maryati"
                            />
                            <div class="name">Maryati</div>
                            <div class="desc">Director, Acme EdTech</div>
                          </div>
                          <!-- <div
                            class="user"
                            v-for="(itm, idx) in Array.isArray(data.tutor)
                              ? data.tutor.slice(0, 1)
                              : []"
                            :key="idx"
                          >
                            <div class="name">{{ itm.name }}</div>
                            <div class="desc">{{ itm.job }}</div>
                          </div> -->
                        </div>
                      </div>
                    </div>
                    <div class="link">
                      <qrcode-vue :value="link_url" :size="45" level="H" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="cert-card" v-else>
            <div class="content">
              <img src="@/assets/img/medal.png" class="medal" />
              <img src="@/assets/img/logo.png" class="logo" />
              <div>
                <div class="number">{{ data.id }}</div>
              </div>
              <div class="txt">Diberikan kepada</div>
              <div class="fullname">{{ data.user.fullname }}</div>
              <div class="txt">
                Atas kelulusannya pada kelas<br />
                <span>{{ data.course_category.name }}</span>
              </div>
              <div class="date">{{ date(data.created_at) }}</div>
              <div class="foot">
                <div class="tutors">
                  <div class="user">
                    <div class="name">Maryati</div>
                    <div class="desc">
                      Chief Executive Officer<br />
                      Acme EdTech
                    </div>
                  </div>
                  <div
                    class="user"
                    v-for="(itm, idx) in Array.isArray(data.tutor)
                      ? data.tutor.slice(0, 1)
                      : []"
                    :key="idx"
                  >
                    <div class="name">{{ itm.name }}</div>
                    <div class="desc">{{ itm.job }}</div>
                  </div>
                </div>
                <div class="link">
                  <qrcode-vue :value="link_url" :size="35" level="H" />
                  <div class="tx">Verifikasi Sertifikat</div>
                  <div class="target">
                    lms.example.com/certificates/{{ data.id }}
                  </div>
                </div>
              </div>
            </div>
            <!-- <img src="@/assets/img/bg-cert.png" class="frame" /> -->
          </div>
          <div class="cta">
            <a class="btn btn-blue btn-sm" @click.stop.prevent="generateReport">
              <v-icon class="mr-2" small>mdi-download</v-icon
              ><span>Unduh Sertifikat</span></a
            >
          </div>
          <input type="hidden" id="testing-code" :value="link_url" />
          <div class="cta">
            <a class="btn-white" @click.stop.prevent="copyURL">
              <span>Copy URL</span></a
            >
          </div>
        </div>
        <div class="no-data" v-else>
          <img
            src="@/assets/img/img-no-course.png"
            alt=""
            style="width: 300px"
          />
          <h1>Data sertifikat <br />tidak ditemukan</h1>
          <div class="cta" style="margin-top: 60px">
            <router-link to="/home" class="btn btn-blue btn-sm">
              <span>Kembali ke Homepage</span></router-link
            >
          </div>
        </div>
      </div>
    </div>
    <vue-html2pdf
      :show-layout="false"
      :float-layout="true"
      :enable-download="true"
      :preview-modal="false"
      :paginate-elements-by-height="1500"
      :filename="filename"
      :pdf-quality="2"
      :manual-pagination="true"
      :pdf-format="[9.63, 11.69]"
      pdf-orientation="landscape"
      cer-card
      pdf-content-width="1125px"
      @hasStartedGeneration="hasStartedGeneration()"
      @hasGenerated="hasGenerated($event)"
      ref="html2Pdf"
    >
      <section slot="pdf-content">
        <section
          class="pdf-item"
          :style="
            data.course_category.type == 'prakerja'
              ? {}
              : { height: '9.63in !important', width: '11.69in !imporant' }
          "
        >
          <div v-if="data.course_category.type == 'prakerja'">
            <div class="cert-card-prakerja-pdf">
              <div class="content-pdf">
                <div
                  style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 34px;
                  "
                >
                  <div class="txt">
                    {{ data.course_category.name }}<br /><br /><span
                      >Diberikan kepada</span
                    >
                  </div>
                  <div class="fullname">{{ data.user.fullname }}</div>
                  <div class="subtitle">
                    Karena telah menyelesaikan pelatihan
                  </div>
                  <div class="foot">
                    <div class="tutors">
                      <div class="user">
                        <img
                          src="@/assets/img/ttd-maryati.png"
                          class="maryati"
                        />
                        <div class="name">Maryati</div>
                        <div class="desc">
                          Chief Executive Officer<br />
                          Acme EdTech
                        </div>
                      </div>
                      <!-- <div
                        class="user"
                        v-for="(itm, idx) in Array.isArray(data.tutor)
                          ? data.tutor.slice(0, 1)
                          : []"
                        :key="idx"
                      >
                        <div class="name">{{ itm.name }}</div>
                        <div class="desc">{{ itm.job }}</div>
                      </div> -->
                    </div>
                  </div>
                </div>
                <div class="link">
                  <qrcode-vue :value="link_url" :size="110" level="H" />
                </div>
              </div>
            </div>
            <div v-if="prakerja_course_type == 'canva'">
              <img src="@/assets/cert/page-2-canva.png" />
            </div>
            <div v-else-if="prakerja_course_type == 'excel'">
              <img src="@/assets/cert/page-2-excel.png" />
            </div>
            <div v-else-if="prakerja_course_type == 'chatgpt'">
              <img src="@/assets/cert/page-2-chatgpt.png" />
            </div>
            <div v-else-if="prakerja_course_type == 'aiart'">
              <img src="@/assets/cert/page-2-aiart.png" />
            </div>
          </div>
          <div
            v-else
            class="cert-card"
            :style="{ height: '9.63in !important', width: '11.69in !imporant' }"
          >
            <div class="content">
              <img src="@/assets/img/medal.png" class="medal" />
              <img src="@/assets/img/logo.png" class="logo" />
              <div>
                <div class="number">{{ data.id }}</div>
              </div>
              <div class="txt">Diberikan kepada</div>
              <div class="fullname">{{ data.user.fullname }}</div>
              <div class="txt">
                Atas kelulusannya pada kelas<br />
                <span>{{ data.course_category.name }}</span>
              </div>
              <div class="date">{{ date(data.created_at) }}</div>
              <div class="foot">
                <div class="tutors">
                  <div class="user">
                    <div class="name">Maryati</div>
                    <div class="desc">
                      Chief Executive Officer<br />
                      Acme EdTech
                    </div>
                  </div>
                  <div
                    class="user"
                    v-for="(itm, idx) in Array.isArray(data.tutor)
                      ? data.tutor.slice(0, 1)
                      : []"
                    :key="idx"
                  >
                    <div class="name">{{ itm.name }}</div>
                    <div class="desc">{{ itm.job }}</div>
                  </div>
                </div>
                <div class="link">
                  <qrcode-vue :value="link_url" :size="110" level="H" />
                  <div class="tx">Verifikasi Sertifikat</div>
                  <div class="target">
                    lms.example.com/certificates/{{ data.id }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </section>
    </vue-html2pdf>
  </div>
</template>

<script>
import QrcodeVue from "qrcode.vue";
import moment from "moment";
import VueHtml2pdf from "vue-html2pdf";
import Loading from "@/components/Loading";

export default {
  name: "CertificatePage",
  metaInfo() {
    return {
      title: "Certificate - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {
    VueHtml2pdf,
    Loading,
    QrcodeVue,
  },

  data() {
    return {
      // certiShow: true,
      show: "1",
      data: {
        user: {},
        tutor: [],
        course_category: {},
      },
      prakerja_course_type: "",
      isReady: false,
      isLoading: false,
      link_url: "",
      alert: "",
      alertOpen: false,
      filename: "",
      usernotfound: false,
    };
  },

  methods: {
    date(val) {
      return moment(new Date(val)).format("DD MMMM YYYY");
    },
    generateReport() {
      this.$refs.html2Pdf.generatePdf();
    },
    copyURL() {
      var vm = this;
      // var Url = vm.link_url
      let testingCodeToCopy = document.querySelector("#testing-code");
      testingCodeToCopy.setAttribute("type", "text"); // 不是 hidden 才能複製
      testingCodeToCopy.select();

      try {
        var successful = document.execCommand("copy");
        var msg = successful ? "successful" : "unsuccessful";
        // let notif = document.querySelector('.alert-notif');
        vm.alert = `Certifcate link was copied ${msg}`;
        vm.alertOpen = true;
        setTimeout(() => {
          vm.alertOpen = false;
        }, 3000);
      } catch (err) {
        alert("Oops, unable to copy");
      }

      /* unselect the range */
      testingCodeToCopy.setAttribute("type", "hidden");
      window.getSelection().removeAllRanges();
    },
    getCert(id) {
      this.loadScreen = true;
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/certificate/read/${id}`,
        })
        .then((res) => {
          if (res.data.success) {
            this.isReady = true;
            this.data = res.data.data.Certificate;

            const courseCategory = this.data.course_category;
            const nameLowerCase = courseCategory.name.toLowerCase();
            const dataAnalitikKeyword = [
              "data analis",
              "analis data",
              "analysis",
            ];
            const AIKeyword = ["artificial intelligence", "kecerdasan buatan"];
            const UIUXKeyword = ["UI/UX", "Desainer"];
            const keyword = {
              da: dataAnalitikKeyword,
              ai: AIKeyword,
              uiux: UIUXKeyword,
            };

            const idToKeywordMap = {
              "40c5b83c-353f-49ac-bc5f-be98d891991a": "da",
              "bb70ad7a-5f08-4133-8c5f-8ffddc6e146e": "ai",
              "f489c062-3200-427f-ab04-32da04710e99": "uiux",
              "056a9a83-e88a-4deb-ae21-d7a5a2f077a6": "chatgpt",
              "16e8e1e6-e3fc-47dd-af54-cbf7080b46d4": "tiktok",
              "520bfae3-1c71-4c58-898e-dcd9a855aec9": "canva",
              "72d066fe-d80c-4aa6-bf93-3d8c588af921": "excel",
              "7f57fd04-6826-4acc-bcde-9d9290b4fb42": "aiart",
            };
            // console.log(nameLowerCase);
            for (const key in keyword) {
              keyword[key].forEach((word) => {
                if (nameLowerCase.includes(word)) {
                  this.prakerja_course_type = key;
                }
              });
            }
            const id = courseCategory.id;
            if (idToKeywordMap[id]) {
              this.prakerja_course_type = idToKeywordMap[id];
            }
            this.show = "1";
            this.isLoading = false;
          } else {
            this.isReady = true;
            this.isLoading = false;
            this.show = "0";
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.loadScreen = false;
        });
    },
  },

  async mounted() {
    this.getCert(this.$route.params.id);
    this.filename = `AcmeLMS_Certificate_${this.$route.params.id}`;
    this.link_url = `${process.env.VUE_APP_API_BASE_URL}/certificate/${this.$route.params.id}`;
  },
  watch: {},
};
</script>

<style lang="scss" scoped>
$font: "Proxima Nova";
$font-desc: "PT Sans";

.alert-notif {
  position: absolute;
  z-index: 99999;
  left: 50%;
  transform: translateX(-50%);
  font-size: 13px;
  padding: 8px 24px;
  border-radius: 35px;
  min-width: 300px;
  top: -120px;
  transition: all 0.7s ease-in-out;
  text-align: center;
}

.alert-notif.open {
  background: #3cb560;
  color: #fff;
  top: 30px;
}

.webapp {
  background: #fafafa;

  .webapp-wrapper {
    max-width: 450px;
    min-height: 100vh;
    width: 100%;
    margin: 0 auto;
    background: #ffffff;
    position: relative;
    padding: 0 20px;
  }
}

.cert-area {
  font-family: $font;

  .section {
    background: #0056d2;
    margin: 0 -20px;
    padding: 2rem 20px 7rem;
    border-radius: 0 0 32px 32px;

    .brand {
      text-align: center;

      img {
        width: 100px;
      }
    }

    .head {
      text-align: center;
      padding: 16px 20px 0;
      font-size: 18px;
      line-height: 24px;
      font-weight: 600;
      color: #fff;
    }
  }

  .prakerja-cert {
    img {
      position: relative;
      margin: -64px 0px 64px 0px;
    }
  }

  .cert-card {
    background: url("../../../assets/img/bg-cert.png");
    background-size: 100% 100%;
    background-position: center;
    box-shadow: 6px 6px 6px rgba(131, 149, 167, 0.16);
    border-radius: 12px;
    padding: 40px;
    margin: -5rem 0px 3rem;
    position: relative;
    color: #121212;

    .content {
      position: relative;
      z-index: 2;

      .logo {
        height: 20px;
        margin: 5px 0 24px;
      }

      .medal {
        position: absolute;
        right: -10px;
        top: 0;
        width: 90px;
      }

      .number {
        color: #ffffff;
        background: #0056d2;
        border-radius: 5px;
        font-size: 5px;
        padding: 0px 5px;
        display: inline-block;
        font-weight: 600;
        margin-bottom: 3px;
      }

      .txt {
        font-size: 10px;
        color: #121212;
        font-weight: 400;

        span {
          color: #0056d2;
        }
      }

      .fullname {
        font-size: 22px;
        color: #0056d2;
        font-weight: 700;
        padding: 5px 0;
        text-transform: capitalize;
      }

      .date {
        font-size: 8px;
        padding: 24px 0 32px;
      }

      .foot {
        display: flex;
        justify-content: space-between;

        .tutors {
          width: 60%;
          display: flex;

          .user {
            margin-right: 12px;

            .name {
              font-weight: 700;
              font-size: 10px;
              color: #121212;
            }

            .desc {
              font-size: 6px;
              color: #666666;
            }
          }
        }

        .link {
          text-align: right;
          width: 40%;

          .tx {
            font-weight: 600;
            font-size: 7px;
            color: #121212;
          }

          .target {
            color: #666666;
            font-size: 4px;
          }
        }
      }
    }

    .frame {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 1;
    }

    .course {
      font-size: 12px;
      color: #222f3e;
      font-weight: bold;
      margin-top: 4px;
    }
  }

  .cert-card-prakerja {
    background: url("../../../assets/img/bg-sertifikat-prakerja.png");
    background-size: 100% 100%;
    background-position: center;
    box-shadow: 6px 6px 6px rgba(131, 149, 167, 0.16);
    border-radius: 12px;
    padding: 35px 18px 10px 18px;
    margin: -5rem 0px 3rem;
    position: relative;
    color: #121212;
    display: flex;
    align-items: center;

    .content {
      position: relative;
      z-index: 2;
      width: 100%;

      .number {
        color: #ffffff;
        background: #0056d2;
        border-radius: 5px;
        font-size: 5px;
        padding: 0px 5px;
        display: inline-block;
        font-weight: 600;
        margin-bottom: 3px;
      }

      .txt {
        font-size: 16px;
        color: #121212;
        font-weight: 700;
        text-align: center;
        max-width: 200px;

        span {
          font-size: 8px;
          color: #121212;
          font-weight: 400;
          text-align: center;
        }
      }

      .subtitle {
        font-size: 8px;
        font-weight: 400;
        text-align: center;
        text-transform: capitalize;
      }

      .fullname {
        font-size: 12px;
        font-weight: 700;
        padding: 5px 0;
        text-align: center;
        text-transform: capitalize;
      }

      .date {
        font-size: 8px;
        padding: 24px 0 32px;
      }

      .foot {
        justify-content: center;
        margin: 12px 0px;

        .tutors {
          display: flex;
          flex-direction: row;
          justify-content: center;

          .user {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: end;
            margin-right: 12px;
            max-width: 180px;

            .maryati {
              width: 60px;
              margin: 0px;
            }

            .name {
              text-align: center;
              font-weight: 700;
              font-size: 9px;
              color: #121212;
            }

            .desc {
              text-align: center;
              font-size: 6px;
              color: #666666;
              min-height: 18px;
            }
          }
        }

        .link {
          text-align: right;
          width: 40%;

          .tx {
            display: flex;
            font-size: 7px;
            color: #121212;
          }

          .target {
            display: flex;
            font-size: 8px;
            color: #666666;
          }
        }
      }
    }

    .frame {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 1;
    }

    .course {
      font-size: 12px;
      color: #222f3e;
      font-weight: bold;
      margin-top: 4px;
    }
  }

  .cta {
    text-align: center;
    margin-bottom: 20px;

    .v-icon {
      color: #fff !important;
    }

    .btn:hover {
      .v-icon {
        color: #0056d2 !important;
      }
    }
  }
}

.pdf-item {
  margin: 0 auto;

  .cert-card {
    background: url("../../../assets/img/bg-cert.png");
    background-size: 100% 100%;
    background-position: center;
    box-shadow: 6px 6px 6px rgba(131, 149, 167, 0.16);
    border-radius: 12px;
    padding: 0px 90px 0px;
    margin: 0px 0px 0;
    position: relative;
    color: #121212;
    min-height: 100vh;
    min-width: 100vh;
    display: flex;
    align-items: center;

    .content {
      position: relative;
      z-index: 2;
      width: 100%;

      .logo {
        height: 54px;
        margin: 20px 0 40px;
      }

      .medal {
        position: absolute;
        right: -20px;
        top: -10px;
        width: 200px;
      }

      .number {
        color: #ffffff;
        background: #0056d2;
        border-radius: 5px;
        font-size: 12px;
        padding: 5px 8px 8px;
        display: inline-block;
        margin-bottom: 10px;
      }

      .txt {
        font-size: 20px;
        color: #121212;
        font-weight: 400;

        span {
          color: #0056d2;
        }
      }

      .fullname {
        font-size: 70px;
        color: #0056d2;
        font-weight: 700;
        padding: 32px 0px;
        text-transform: capitalize;
      }

      .date {
        font-size: 16px;
        padding: 60px 0 60px;
      }

      .foot {
        display: flex;
        justify-content: space-between;

        .tutors {
          width: 50%;
          display: flex;

          .user {
            margin-right: 24px;

            .name {
              font-weight: 700;
              font-size: 18px;
              color: #121212;
              margin-bottom: 6px;
            }

            .desc {
              font-size: 14px;
              color: #666666;
            }
          }
        }

        .link {
          text-align: right;
          width: 50%;

          .tx {
            font-weight: 600;
            font-size: 16px;
            color: #121212;
            margin-bottom: 6px;
          }

          .target {
            color: #666666;
            font-size: 11px;
          }
        }
      }
    }

    .frame {
      position: absolute;
      top: 0;
      left: 0;
      height: 100vh;
      width: 100%;
      z-index: 1;
    }

    .course {
      font-size: 12px;
      color: #222f3e;
      font-weight: bold;
      margin-top: 4px;
    }
  }

  .cert-card-prakerja-pdf {
    background: url("../../../assets/img/bg-sertifikat-prakerja.png");
    background-size: 100% 100%;
    background-position: center;
    box-shadow: 6px 6px 6px rgba(131, 149, 167, 0.16);
    border-radius: 12px;
    padding: 130px 50px;
    margin: 0 auto;
    position: relative;
    color: #121212;
    display: flex;
    align-items: center;

    .content-pdf {
      position: relative;
      z-index: 2;
      width: 100%;

      .txt {
        font-size: 44px;
        color: #121212;
        font-weight: 700;
        text-align: center;
        max-width: 650px;

        span {
          font-size: 18px;
          color: #121212;
          font-weight: 400;
          text-align: center;
        }
      }

      .subtitle {
        font-size: 18px;
        font-weight: 400;
        text-align: center;
        text-transform: capitalize;
      }

      .fullname {
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        text-transform: capitalize;
      }

      .foot {
        display: flex;
        justify-content: center;

        .tutors {
          display: flex;
          gap: 32px;

          .user {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: end;
            gap: 4px;

            .maryati {
              width: 200px;
            }

            .name {
              text-align: center;
              font-weight: 700;
              font-size: 20px;
              color: #121212;
            }

            .desc {
              min-height: 48px;
              text-align: center;
              font-size: 16px;
              color: #666666;
            }
          }
        }

        .link {
          text-align: right;
          width: 40%;

          .tx {
            display: flex;
            font-size: 7px;
            color: #121212;
          }

          .target {
            display: flex;
            font-size: 8px;
            color: #666666;
          }
        }
      }
    }

    .frame {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 1;
    }

    .course {
      font-size: 12px;
      color: #222f3e;
      font-weight: bold;
      margin-top: 4px;
    }
  }
}

.no-data {
  text-align: center;
  padding-top: 5rem;

  img {
    margin-bottom: 16px;
  }

  h1 {
    font-size: 20px;
    font-weight: bold;
    color: #000;
    margin-bottom: 8px;
  }

  p {
    font-size: 16px;
    color: #576574;
  }
}

#back-icon {
  position: absolute;
  left: 2rem;
  top: 1.5rem;
  background: #ffffff;
  box-shadow: 0px 16px 40px rgba(112, 144, 176, 0.2);
  border-radius: 10px;
  padding: 8px;

  img {
    height: 18px;
    margin-bottom: -4px;
  }
}
</style>

<style>
.vue-html2pdf .pdf-preview {
  box-shadow: none !important;
}
</style>
