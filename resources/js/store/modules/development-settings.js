import api from "@/api";
import {API} from "@/api/constants-api";

export default {
  namespaced: true,

  state: {
    panelOpen: false,
    roles: null,
  },

  getters: {
    panelOpen: state => state.panelOpen,
    roles: state => state.roles,
    role: (state) => (id) => {if (id !== null && state.roles !== null) return state.roles.find((item) => item.id === id)}
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
    setRoles(state, payload) {
      state.roles = payload;
    }
  },

  actions: {
    toggle({commit, state}, payload) {
      if (payload !== undefined) {
        commit('set', payload)
      } else {
        state.panelOpen ? commit('hide') : commit('show');
      }
    },
    setRoles({commit, state}) {
      if (state.roles === null) {
        api.get(API.ROLES).then(({data}) => {
          commit('setRoles', data.data)
        });
      }
    },
  }
};
