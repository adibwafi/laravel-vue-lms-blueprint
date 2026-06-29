<template>
  <div
    class="alert-notif"
    :class="{ danger: notifdanger, success: notifsuccess }"
  >
    {{ textNotif }}
  </div>
</template>

<script>
export default {
  name: "AlertComponent",
  props: ["notifdanger", "notifsuccess", "textNotif"],
  data() {
    return {};
  },
  mounted() {},
};
</script>
