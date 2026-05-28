import Vue from 'vue';
import theme from "@/plugins/vuetify";

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
const colors = theme.framework.theme.themes.light;

const options = {
  confirmButtonColor: colors.primary,
  cancelButtonColor: colors.secondary,
  denyButtonColor: colors.error,
};

Vue.use(VueSweetalert2, options);
