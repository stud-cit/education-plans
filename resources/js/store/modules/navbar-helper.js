export default {
  namespaced: true,

  state: {
    panelOpen: false,
  },

  getters: {
    panelOpen: state => state.panelOpen,
  },

  mutations: {
    show(state){
      state.panelOpen = true;
    },
    hide(state){
      state.panelOpen = false;
    },
    set(state, payload) {
      state.panelOpen = payload;
    },
  },

  actions: {
    toggle({commit, state}, payload) {
      if (payload !== undefined) {
        commit('set', payload)

      } else {
        state.panelOpen ? commit('hide') : commit('show');
      }
    },
  }
};
