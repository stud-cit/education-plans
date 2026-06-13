import Vue from 'vue'
import App from './App.vue'
import store from './store'
import router from './router'
import { registerGuards } from './router/guards'
import vuetify from './plugins/vuetify'

import '@/plugins/vee-validate'
import '@/plugins/sweet-alert-2'
import '@/assets/styles/base.css'
import 'roboto-fontface/css/roboto/roboto-fontface.css'
import '@mdi/font/css/materialdesignicons.css'
import GlobalMixin from "@/mixins/GlobalMixin";
import btnTooltip from "@c/base/BtnTooltip";

Vue.component('btn-tooltip', btnTooltip);

Vue.config.productionTip = false
Vue.mixin(GlobalMixin);

export const eventBus = new Vue()

const app = new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
});

// Register guards AFTER the Vue instance is created
// to ensure router and store are fully initialized.
registerGuards(router);

app.$mount('#app')
