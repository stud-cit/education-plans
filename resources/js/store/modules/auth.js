import api from '@/api/index';
import {API} from "@/api/constants-api";

export default {
    namespaced: true,

    state: {
      userData: null
    },

    getters: {
        user: state => state.userData
    },

    mutations: {
      setUserData(state, user) {
        const newUser = JSON.stringify(user);
        // if(localStorage.getItem('user') !== newUser) {
          localStorage.setItem('user', newUser);
          state.userData = user;
        // }
      }
    },

    actions: {
      async getUserData({ commit }) {
        const {data, status} = await api.get(API.USER, false , {showLoader: true})
          .catch(() => {
            localStorage.removeItem("user");
            localStorage.removeItem("authToken");
            localStorage.removeItem("cabinetToken");
          });
        if (status === 200) {
          commit("setUserData", data);
        }
      },
        sendLoginRequest({ commit }, data) {
            commit("setErrors", {}, { root: true });
            return api.post(API.LOGIN, data)
                .then(response => {
                    if (response.status === 200) {
                        commit("setUserData", response.data.user);
                        localStorage.setItem('user', JSON.stringify(response.data.user));
                        localStorage.setItem("authToken", response.data.token);
                    }
                })

        },
        // sendRegisterRequest({ commit }, data) {
        //   commit("setErrors", {}, { root: true });
        //   return api
        //     .post(API.REGISTER, data)
        //     .then(response => {
        //       commit("setUserData", response.data.user);
        //       localStorage.setItem("authToken", response.data.token);
        //     });
        // },
        async sendLogoutRequest({ commit }) {
            await api
                .get(API.LOGOUT)
                .then((response) => {
                    if (response.status === 200) {
                        commit("setUserData", null);
                        localStorage.removeItem("user");
                        localStorage.removeItem("authToken");
                        localStorage.removeItem("cabinetToken");
                    }
                })
        },
        // sendVerifyResendRequest() {
        //   return axios.get(process.env.VUE_APP_API_URL + "email/resend");
        // },
        // sendVerifyRequest({ dispatch }, hash) {
        //   return axios
        //     .get(process.env.VUE_APP_API_URL + "email/verify/" + hash)
        //     .then(() => {
        //       dispatch("getUserData");
        //     });
        // }
    }
};
