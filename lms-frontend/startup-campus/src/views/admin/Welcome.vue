<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div>
    <Header />
    <div class="content-main-area">
      <div
        class="alert-notif"
        :class="{ danger: notifdanger, success: notifsuccess }"
      >
        {{ textNotif }}
      </div>
      <div class="k-wrapping">
        <div class="k-dashboard">
          <Sidebar />
          <div class="k-content">
            <div class="k-card">
              <div class="k-head">
                <h2>Startup Campus - CMS</h2>
                <div class="k-desc">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                  Aliquam malesuada justo augue, non aliquam arcu mattis
                  vestibulum. Nullam id urna mi. Nullam sit amet lectus sapien
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
import Header from "./../../components/HeaderCMS";
import Sidebar from "./../../components/SidebarCMS";
export default {
  name: "WelcomeAdminPage",
  metaInfo() {
    return {
      title: "Welcome - AcmeLMS",
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
  },

  data() {
    return {
      isSplash: true,
      // notif
      notifsuccess: false,
      notifdanger: false,
      textNotif: "",
    };
  },
  methods: {
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
    getMainData() {
      this.isLoading = true;
      this.$root
        .axios({
          method: "get",
          url: `/v1/admin/course-category`,
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
  },
  mounted() {
    this.getMainData();
    this.$root.cms_tab = "default";
  },
};
</script>
