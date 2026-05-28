import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
import plans from "./modules/plans";
import loader from "@/store/modules/loader";
import auth from './modules/auth'
import developmentSettings from "@/store/modules/development-settings";
import navbarHelper from "@/store/modules/navbar-helper";

export default new Vuex.Store({
  state: {
    errors: {}
  },
  getters: {
    errors: state => state.errors
  },
  mutations: {
    setErrors(state, errors) {
      state.errors = errors;
    }
  },
  actions: {},
  modules: {
    auth,
    plans,
    loader,
    developmentSettings,
    navbarHelper
  }
})
