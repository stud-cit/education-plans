<template>
  <div v-if="show" class="loader">
    <svg
      version="1.1"
      id="L4"
      xmlns="http://www.w3.org/2000/svg"
      xmlns:xlink="http://www.w3.org/1999/xlink"
      x="0px"
      y="0px"
      viewBox="0 0 50 100"
      enable-background="new 0 0 0 0"
      xml:space="preserve"
    >
      <circle fill="#fff" stroke="none" cx="6" cy="50" r="6">
        <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.1" />
      </circle>
      <circle fill="#fff" stroke="none" cx="26" cy="50" r="6">
        <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.2" />
      </circle>
      <circle fill="#fff" stroke="none" cx="46" cy="50" r="6">
        <animate attributeName="opacity" dur="1s" values="0;1;0" repeatCount="indefinite" begin="0.3" />
      </circle>
    </svg>
  </div>
</template>

<script>
export default {
  name: 'Loader',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
  },
  watch: {
    show: (v) => {
      const html = document.getElementsByTagName('html')[0];
      if (v) {
        html.style.overflow = 'hidden';
      } else {
        html.style.overflow = 'unset';
      }
    },
  },
};
</script>

<style scoped>
.loader {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: url('../assets/logo-sumdu.svg'), rgba(255, 255, 255, 1);
  background-repeat: no-repeat;
  background-position: 50% 45%;
  text-align: center;
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}
svg {
  width: 100px;
  height: 100px;
  margin: 20px;
  display: inline-block;
  position: relative;
  top: 25px;
}
svg circle {
  fill: #001f5a;
}
</style>
