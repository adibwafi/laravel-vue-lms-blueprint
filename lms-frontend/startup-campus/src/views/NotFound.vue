<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <v-fade-transition>
    <div class="main-container">
      <div class="notfound">
        <div class="head">404</div>
        <div class="head2">Halaman tidak tersedia</div>
        <div class="desc">
          Mohon maaf, halaman yang anda tuju saat ini belum tersedia :(
        </div>
        <div class="cta">
          <router-link to="/course" class="btn btn-blue btn-block"
            >Pilih Kelasmu</router-link
          >
          <router-link to="/home" class="btn btn-white-ot btn-block"
            >Kembali ke Beranda</router-link
          >
        </div>
      </div>
    </div>
  </v-fade-transition>
</template>

<style lang="scss" scoped>
$main-color: #0056d2;

.main-container {
  background: #f6f8fd;

  .notfound {
    text-align: center;
    margin-top: 3rem;
    .head {
      font-weight: 700;
      font-size: 120px;
      line-height: 151px;
      text-align: center;
      color: #2b2c27;
    }
    .head2 {
      font-weight: 700;
      font-size: 30px;
      line-height: 38px;
      text-align: center;
      color: #2b2c27;
      margin-top: -15px;
    }
    .desc {
      font-size: 18px;
      line-height: 30px;
      color: rgba(43, 44, 39, 0.5);
      padding: 40px 0 60px;
    }
    .cta {
      .btn {
        margin-bottom: 16px;
      }
    }
  }
}
</style>
