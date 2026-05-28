export default {
  namespaced: true,

  state: {
    loading: false,
  },

  getters: {},

  mutations: {
    show(state) {
      state.loading = true;
    },
    hide(state) {
      state.loading = false;
    },
  },

  actions: {
    show({ commit }) {
      commit('show');
    },
    hide({ commit }) {
      commit('hide');
    },
  },
};
