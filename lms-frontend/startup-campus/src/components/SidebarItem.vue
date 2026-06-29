<template>
  <a :href="to" v-if="isExternal" target="_blank" rel="noopener noreferrer">
    <div :class="[highlighted ? 'sidebar-atoms-highlighted' : 'sidebar-atoms']">
      <img
        :src="require(`@/assets/img/${highlighted ? iconHighlighted : icon}`)"
        alt="Icon"
        class="logo"
      />
      <p :class="[highlighted ? 'typography-highlighted' : 'typography']">
        {{ label }}
      </p>
    </div>
  </a>
  <router-link :to="to" v-else>
    <div :class="[highlighted ? 'sidebar-atoms-highlighted' : 'sidebar-atoms']">
      <img
        :src="require(`@/assets/img/${highlighted ? iconHighlighted : icon}`)"
        alt="Icon"
        class="logo"
      />
      <p :class="[highlighted ? 'typography-highlighted' : 'typography']">
        {{ label }}
      </p>
    </div>
  </router-link>
</template>

<script>
export default {
  name: "SidebarItem",
  props: {
    icon: {
      type: String,
      required: true,
    },
    iconHighlighted: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    to: {
      type: String,
      required: true,
    },
    highlighted: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    isExternal() {
      return this.to.startsWith("http");
    },
  },
};
</script>

<style scoped>
.sidebar-atoms {
  display: flex;
  width: 100%;
  padding: 12px 32px;
  align-items: center;
  gap: 12px;
  flex-direction: row;

  .logo {
    max-width: 24px;
    height: auto;
  }

  .typography {
    margin-bottom: 0;
    font-size: 18px;
    font-weight: 500;
    line-height: 180%;
    color: var(--Black-Scale-Black-300, #8f8f8f);
  }
}

.sidebar-atoms-highlighted {
  display: flex;
  width: 100%;
  padding: 12px 32px;
  align-items: center;
  gap: 12px;
  flex-direction: row;
  border-radius: 8px;
  background: linear-gradient(90deg, #0056d2 0%, #14a2ff 100%);

  .logo {
    max-width: 24px;
    height: auto;
  }

  .typography-highlighted {
    margin-bottom: 0;
    font-size: 18px;
    font-weight: 500;
    line-height: 180%;
    color: white !important;
  }
}
</style>
