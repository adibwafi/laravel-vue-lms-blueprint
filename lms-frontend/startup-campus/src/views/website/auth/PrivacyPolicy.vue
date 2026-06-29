<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <Loading :isLoading="isLoading" />
      <Alert
        :notifdanger="notifdanger"
        :notifsuccess="notifsuccess"
        :textNotif="textNotif"
      />

      <img src="@/assets/img/ellipse-auth.png" alt="/" class="bg-ellipse" />
      <div class="back">
        <router-link to="/auth/register">
          <img src="@/assets/img/icn-back.png" alt="" />
        </router-link>
      </div>
      <div class="auth-area">
        <div class="heading">Kebijakan Privasi</div>
        <div class="desc-heading">Terakhir Diubah: 29 Januari 2024</div>

        <div class="desc">
          <strong>PT Acme EdTech</strong> sebagai pemilik dan pengelola situs
          <a
            href="https://acmeedtech.example.com/"
            target="_blank"
            style="text-decoration: underline"
            >www.acmeedtech.example.com</a
          >

          (“<strong>Acme EdTech</strong>” atau “<strong>Kami</strong>”) berupaya
          menjunjung tinggi perlindungan atas privasi data yang dimiliki oleh
          Pengguna. Kebijakan Privasi ini ditujukan untuk memberikan informasi
          kepada Pengguna mengenai cara Kami mengumpulkan, menggunakan,
          membagikan, memproses dan mengamankan Data Pribadi Pengguna situs Acme
          EdTech. Informasi pribadi Pengguna pada saat mendaftar di situs Kami
          beserta catatan aktivitas pada saat menggunakan layanan di situs Acme
          EdTech akan dicatat dan disimpan dalam platform dan dijaga
          kerahasiaannya. Pada saat Pengguna menghubungi Kami, maka Kami
          menyimpan catatan mengenai korespondensi tersebut dan isi dari
          komunikasi antara Pengguna dan Kami. Kami akan memberikan
          pemberitahuan kepada Pengguna jika terjadi perubahan pada Kebijakan
          Privasi ini dengan mempostingnya pada laman ini dan/atau menuliskan
          tanggal pembaharuan kebijakan Privasi terakhir. Dalam hal mengakses
          dan/atau menggunakan dan/atau mendaftar akun pada situs Kami, apabila
          Pengguna berusia di bawah 18 tahun mohon pastikan anda telah
          mendiskusikan Kebijakan Privasi ini dengan orang tua, wali atau
          pengampu dalam menyetujui Kebijakan Privasi ini. Persetujuan termasuk
          persetujuan atas segala praktik yang dilakukan atas Data Pribadi
          sebagaimana disebutkan pada Kebijakan Privasi, termasuk namun tidak
          terbatas pada proses perolehan dan pengumpulan Data Pribadi,
          pengolahan dan penganalisisan Data Pribadi, penyimpanan, penampilan,
          pengumuman, pengiriman, penyebarluasan dan/atau pembukaan akses dan
          pemusnahan atas segala Data Pribadi sesuai dengan Kebijakan Privasi
          ini dan peraturan perundang-undangan yang berlaku di Indonesia.
          Kerahasiaan akun dan kata sandi (password) merupakan tanggung jawab
          masing-masing Pengguna. Kami tidak bertanggung jawab atas kerugian
          yang dapat ditimbulkan akibat kelalaian Pengguna dalam menjaga
          kerahasiaan password.<br /><br />
          <strong>DATA PRIBADI YANG KAMI HIMPUN ATAU<br />KUMPULKAN</strong
          ><br /><br />Data Pribadi yang Kami himpun atau kumpulkan dan simpan
          mencakup namun tidak terbatas pada:<br />
          <ol class="numbered-list">
            <li>
              Informasi Data Pribadi yang Pengguna berikan saat membuat akun di
              situs Acme EdTech. Secara langsung Pengguna memberikan informasi,
              termasuk namun tidak terbatas pada informasi terkait pembuatan
              akun, seperti nama lengkap Pengguna, alamat email, nomor telepon
              dan password.
            </li>
            <li>
              Informasi Data Pribadi yang Kami kumpulkan Kami secara otomatis
              mengumpulkan beberapa informasi saat Pengguna mengunjungi,
              menggunakan, atau menavigasi situs Kami. Informasi ini tidak
              mengungkapkan identitas spesifik Pengguna (seperti nama atau
              informasi kontak Pengguna), tetapi dapat mencakup informasi
              perangkat dan penggunaan, seperti alamat IP, karakteristik browser
              dan perangkat, sistem operasi, preferensi bahasa, URL yang
              merujuk, nama perangkat, negara, lokasi, informasi tentang
              bagaimana dan kapan Pengguna menggunakan situs Kami, dan informasi
              teknis lainnya. Informasi ini utamanya diperlukan untuk menjaga
              keamanan dan operasi situs Kami, serta untuk tujuan analisis dan
              pelaporan internal.
            </li>
            <li>
              Informasi yang Kami kumpulkan dari sumber lain. Kami berhak
              mengumpulkan informasi-informasi Pengguna pada saat Pengguna
              menggunakan situs maupun aplikasi lain yang Kami kelola juga
              secara mandiri atau bekerja sama dengan pihak ketiga (termasuk
              namun tidak terbatas pada misalnya, program referral, mitra
              bisnis, sub-kontraktor dalam pelayanan teknis, jasa pembayaran dan
              pengantaran, jasa periklanan, penyedia analisis). Kami akan
              mengambil langkah-langkah dalam batas kewajaran untuk melakukan
              verifikasi terhadap informasi yang kami kumpulkan dari sumber lain
              sesuai dengan peraturan perundang-undangan yang berlaku di
              Indonesia.
            </li>
          </ol>
          <br /><br />
          <strong>PENGGUNAAN DATA PRIBADI</strong><br /><br />
          <ol>
            <li>
              Kami berhak menggunakan Data Pribadi yang telah anda berikan atau
              kami terima dengan izin Pengguna, untuk menyediakan, meningkatkan,
              dan mengelola situs Kami, berkomunikasi dengan Pengguna, untuk
              keamanan dan pencegahan penipuan, dan untuk mematuhi hukum. Kami
              juga dapat memproses informasi Pengguna untuk tujuan lain dengan
              izin Pengguna. Kami memproses informasi pribadi Pengguna untuk
              berbagai alasan, tergantung pada bagaimana Pengguna berinteraksi
              dengan situs Kami, termasuk namun tidak terbatas pada:
            </li>
            <li>
              Memfasilitasi pembuatan akun dan otentikasi serta mengelola akun
              pengguna lainnya. Kami dapat memproses informasi Pengguna sehingga
              Pengguna dapat membuat dan masuk ke akun Pengguna, serta menjaga
              agar akun Pengguna tetap berfungsi.
            </li>
            <li>
              Untuk melindungi situs kami. Kami dapat memproses informasi
              Pengguna sebagai bagian dari upaya kami untuk menjaga situs Kami
              tetap aman dan aman, termasuk pemantauan dan pencegahan penipuan.
            </li>
            <li>
              Untuk mengevaluasi dan meningkatkan situs Kami, produk, pemasaran,
              dan pengalaman Pengguna. Kami dapat memproses informasi Pengguna
              ketika Kami percaya itu perlu untuk mengidentifikasi tren
              penggunaan, menentukan efektivitas kampanye promosi kami, dan
              mengevaluasi serta meningkatkan situs kami, produk, pemasaran, dan
              pengalaman Pengguna.
            </li>
            <li>
              Untuk mematuhi kewajiban hukum Kami. Kami dapat memproses
              informasi Pengguna untuk mematuhi kewajiban hukum Kami, merespons
              permintaan hukum, dan menjalankan, mendirikan, atau membela hak
              hukum Kami.
            </li>
          </ol>
          <br /><br />
          <strong>PENGGUNAAN DATA PRIBADI</strong><br /><br />Kami dapat
          membagikan informasi dalam situasi khusus yang dijelaskan dalam bagian
          ini dan/atau dengan kategori pihak ketiga berikut. Pemasok, Konsultan,
          dan Penyedia layanan serta Pihak Ketiga lainnya. Kami dapat membagikan
          data anda kepada pihak ketiga pemasok, penyedia layanan, kontraktor,
          atau agen ("pihak ketiga") yang melakukan layanan untuk kami atau atas
          nama kami dan memerlukan akses ke informasi tersebut untuk melakukan
          pekerjaan tersebut. Kami memiliki kontrak dengan pihak ketiga Kami,
          yang dirancang untuk membantu melindungi informasi pribadi Pengguna.
          Ini berarti bahwa mereka tidak dapat melakukan apa pun dengan
          informasi pribadi Pengguna kecuali jika kami telah memberi instruksi
          kepada mereka untuk melakukannya. Mereka juga tidak akan membagikan
          informasi pribadi Pengguna dengan organisasi lain selain Kami. Mereka
          juga berkomitmen untuk melindungi data yang mereka pegang atas nama
          Kami dan menyimpannya selama periode yang kami tentukan. Kategori
          pihak ketiga yang mungkin kami bagikan informasi pribadi adalah
          sebagai berikut:<br />
          <ol>
            <li>Penyedia Layanan Penyimpanan Data.</li>
            <li>Layanan Analitik Data.</li>
            <li>Layanan Komputasi Awan.</li>
            <li>Penyedia Layanan Hosting Website.</li>
            <li>Alat Pemantauan Kinerja.</li>
          </ol>
          <br /><br />Kami juga mungkin perlu membagikan informasi pribadi
          Pengguna dalam situasi berikut:<br />
          <ol>
            <li>
              Transaksi Bisnis. Kami dapat membagikan atau mentransfer informasi
              Pengguna dalam hubungan dengan, atau selama negosiasi, merger,
              penjualan aset perusahaan, pembiayaan, atau akuisisi seluruh atau
              sebagian bisnis kami kepada perusahaan lain.
            </li>
            <li>
              Mitra Bisnis. Kami dapat membagikan informasi Pengguna kepada
              mitra bisnis kami untuk menawarkan produk, layanan, atau promosi
              tertentu kepada anda.
            </li>
          </ol>
          <br /><br />
          <strong>PERIODE PENYIMPANAN DATA PRIBADI</strong><br /><br />
          Kami menyimpan informasi Pengguna selama yang diperlukan untuk
          memenuhi tujuan yang dijelaskan dalam pemberitahuan privasi ini,
          kecuali jika diwajibkan oleh hukum. Kami hanya akan menyimpan
          informasi pribadi Pengguna selama yang diperlukan untuk tujuan yang
          diuraikan dalam pemberitahuan privasi ini, kecuali ada periode retensi
          yang lebih lama yang diwajibkan atau diizinkan oleh hukum (seperti
          persyaratan pajak, akuntansi, atau hukum lainnya). Tidak ada tujuan
          dalam pemberitahuan ini yang akan memerlukan kami menyimpan informasi
          pribadi Pengguna lebih lama dari periode waktu di mana Pengguna
          memiliki akun dengan Kami. Ketika Kami tidak memiliki kebutuhan bisnis
          yang sah untuk memproses informasi pribadi Pengguna, Kami akan
          menghapus atau menganonimkan informasi tersebut, atau jika ini tidak
          mungkin (misalnya, karena informasi pribadi anda telah disimpan dalam
          arsip cadangan), maka kami akan menyimpan informasi pribadi anda
          dengan aman dan mengisolasi informasi tersebut dari pemrosesan lebih
          lanjut sampai penghapusan dimungkinkan.<br /><br />
          <strong>HAK PENGGUNA</strong><br /><br />
          Pengguna dapat meninjau, mengubah, atau mengakhiri akun anda kapan
          saja. Mencabut persetujuan anda: Jika kami mengandalkan pada
          persetujuan anda untuk memproses informasi pribadi anda, yang mungkin
          merupakan persetujuan eksplisit dan/atau tersirat tergantung pada
          hukum yang berlaku, Pengguna berhak untuk mencabut persetujuan anda
          kapan saja. Jika Pengguna memiliki pertanyaan atau komentar tentang
          hak privasi anda, Pengguna dapat mengirim email kepada kami di
          <span style="text-decoration: underline">
            <a href="mailto:info@acmeedtech.example.com"
              >info@acmeedtech.example.com</a
            >
          </span>

          <br /><br />
          <strong>HAK KEKAYAAN INTELEKTUAL</strong><br /><br />
          Hak Kekayaan Intelektual Kami telah terdaftar dan dilindungi oleh
          peraturan perundang-undangan yang terkait Hak Kekayaan Intelektual.
          Segala data yang berhubungan dengan Data Pribadi yang Pengguna
          kirimkan, berikan, transmisikan, unggah atau sediakan pada saat
          menggunakan situs Kami tetap menjadi milik Pengguna.<br /><br />
          <strong>FITUR DO NOT TRACK</strong><br /><br />
          Sebagian besar peramban web dan beberapa sistem operasi seluler dan
          aplikasi seluler termasuk fitur atau pengaturan Do-Not-Track ("DNT")
          yang dapat Pengguna aktifkan untuk menyatakan preferensi privasi anda
          untuk tidak memonitor dan mengumpulkan data tentang aktivitas
          penelusuran online anda. Pada tahap ini, belum ada standar teknologi
          seragam untuk mengenali dan mengimplementasikan sinyal DNT. Oleh
          karena itu, saat ini Kami tidak merespons sinyal peramban DNT atau
          mekanisme lain yang secara otomatis mengkomunikasikan pilihan anda
          untuk tidak dilacak secara online. Jika standar pelacakan online
          diadopsi yang harus kami ikuti di masa depan, kami akan memberi tahu
          anda tentang praktik tersebut dalam versi revisi pemberitahuan
          Kebijakan Privasi ini.<br /><br />
          <strong>COOKIES</strong><br /><br />
          Kami akan menggunakan Cookies selama pengguna menggunakan situs Kami.
          Kami tidak menggabungkan informasi umum yang kami dapatkan melalui
          cookies dengan Data Pribadi yang telah Kami dapatkan. Untuk
          mendapatkan pengalaman yang lebih baik dalam menggunakan situs Kami,
          maka kami menyarankan Pengguna untuk tetap mengaktifkan fitur cookie
          tersebut.<br /><br />
          <strong>PERUBAHAN KEBIJAKAN PRIVASI</strong><br /><br />
          Acme EdTech berhak untuk mengubah, memodifikasi, menambahkan atau
          mengatur ulang Kebijakan Privasi dari waktu ke waktu dan setiap
          perubahan akan ditampilkan pada halaman ini. Dengan tetap menggunakan
          Situs Kami setelah adanya perubahan, modifikasi dan/atau penambahan
          serta pengurangan, Pengguna memberikan persetujuan terkait setiap dan
          seluruh hal tersebut.<br /><br />
          <strong>INFORMASI LEBIH LANJUT</strong><br /><br />Jika Anda memiliki
          pertanyaan atau komentar tentang pemberitahuan ini, Anda dapat
          menghubungi kami melalui email di
          <span style="text-decoration: underline">
            <a href="mailto:info@acmeedtech.example.com"
              >info@acmeedtech.example.com</a
            >
          </span>
        </div>

        <div class="cta">
          <router-link to="/auth/register">
            <button class="btn btn-blue btn-block">Kembali</button>
          </router-link>
        </div>
      </div>
    </div>
  </v-fade-transition>
</template>

<script>
import Loading from "@/components/Loading";
import Alert from "@/components/Alert.vue";
export default {
  name: "PrivacyPolicyPage",
  metaInfo() {
    return {
      title: "Privacy Policy - AcmeLMS",
      meta: [
        {
          name: "robots",
          content: "noindex, nofollow",
        },
      ],
    };
  },
  components: {
    Loading,
    Alert,
  },
  data() {
    return {
      isLoading: false,
      showPass: false,
      checkbox: false,
      valid: false,
      fullname: "",
      email: "",
      phone: "",
      password: "",
      emailRules: [
        (v) => !!v || "E-mail wajib diisi",
        (v) => /.+@.+\..+/.test(v) || "E-mail harus valid",
      ],
      phoneRules: [
        (v) => !!v || "Nomor HP wajib diisi",
        (v) => v.length >= 10 || "Min 10 numbers",
      ],
      passRules: [
        (v) => !!v || "Kata sandi wajib diisi",
        (v) =>
          /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&()_+\-=[\]{};':"\\|,.<>/?`~*]{8,}$/.test(
            v
          ) ||
          "Harus mengandung setidaknya satu angka dan satu huruf besar dan kecil, dan setidaknya 8 karakter atau lebih",
      ],

      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
    };
  },
  methods: {
    validate() {
      event.preventDefault();
      if (this.$refs.form.validate()) {
        this.isLoading = true;
        var data = {
          fullname: this.fullname,
          phone: this.phone,
          email: this.email,
          password: this.password,
          url: `${window.location.origin}/auth/verify-otp`,
        };
        this.$root
          .axios({
            method: "post",
            url: `/v1/auth/register`,
            data: data,
          })
          .then((res) => {
            // console.log(res);
            if (res.data.success) {
              window.localStorage.setItem("emailregist", res.data.data.email);
              window.localStorage.setItem("typeotp", "verify-email");
              this.alert(res.data.message, "success");
              setTimeout(() => {
                this.$router.push(`/auth/verification`);
              }, 3000);
            } else {
              // console.log(res.data.message);
              if (res.data.code == 1) {
                this.alert(res.data.data.email[0], "danger");
              }
            }
            this.isLoading = false;
          })
          .catch((e) => {
            this.alert(e.message, "danger");
            this.isLoading = false;
          });
      }
    },
    alert(msg, alert) {
      if (alert == "success") {
        this.notifsuccess = true;
      } else {
        this.notifdanger = true;
      }
      this.textNotif = msg;
      setTimeout(() => {
        this.notifsuccess = false;
        this.notifdanger = false;
      }, 3000);
    },
  },
};
</script>

<style lang="scss" scoped>
.main-container {
  .bg-ellipse {
    position: absolute;
    top: 0;
    left: 0;
    width: 65%;
  }

  .back {
    position: absolute;
    top: 30px;
    left: 20px;

    img {
      width: 32px;
    }
  }

  .auth-area {
    padding: 100px 20px 40px;
    width: 100%;

    .desc-heading {
      color: var(--Black, #2b2c27);
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 40px;
    }

    .heading {
      color: #2b2c27;
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 24px;
    }

    .desc {
      color: #2b2c27;
      font-size: 14px;
      font-weight: 400;
      line-height: 180%;
      text-align: justify;
    }

    .cta {
      margin: 20px 0 48px;
    }

    .numbered-list {
      padding-left: 20px;
      /* Sesuaikan dengan indentasi yang diinginkan */
    }

    .numbered-list li {
      list-style-type: decimal;
      /* Menggunakan numbering desimal */
    }

    .numbered-list li.circle {
      list-style-type: circle;
      /* Menggunakan simbol lingkaran */
    }
  }
}
</style>
