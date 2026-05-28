import axios from 'axios';
import vuexStore from '@/store';
import router from '@/router';
import jsonToQuery from 'json-to-query-string';

const api = axios.create({
  baseURL: '/api/',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

api.interceptors.response.use(
  (response) => {
    if (response.config.showLoader) {
      vuexStore.dispatch('loader/hide');
    }

    return response;
  },

  (error) => {
    let response = error.response;

    if (response.config.showLoader) {
      vuexStore.dispatch('loader/hide');
    }
    switch (error.response.status) {
      case 400: {
        console.error(error.response.statusText);
        break;
      }
      case 401: {
        localStorage.removeItem('cabinetToken');

        window.location.replace(
          process.env.VUE_APP_CABINET_APP_URL +
            process.env.VUE_APP_CABINET_APP_SERVICE +
            process.env.VUE_APP_CABINET_APP_TOKEN,
        );
        //vuexStore.commit("auth/setUserData", null);
        //localStorage.removeItem("authToken");
        //router.push({ name: "Unauthorized" });
        break;
      }
      case 403: {
        router.push({ name: 'Forbidden' });
        break;
      }
      case 404: {
        router.push({ name: 'NotFoundPage' });
        break;
      }
      case 422: {
        console.log('this error', error.response);
        vuexStore.commit('setErrors', error.response.data.errors); //TODO: this error preview
        break;
      }
      case 500: {
        vuexStore.commit('setErrors', { error: error.response.data.message });
        console.error(error.response.data.message);
        break;
      }
      case 503: {
        router.push({ name: 'MaintenanceMode' });
        break;
      }
      default: {
        return Promise.reject(error);
      }
    }

    // if (error.response.status === 400) {
    //     console.error(error.response.statusText);
    // } else if (error.response.status === 403) {
    //     router.push({ name: 'Forbidden'});
    // } else if (error.response.status === 422) {
    //     console.log('this error', error.response)
    //   vuexStore.commit("setErrors", error.response.data.errors); //TODO: this error preview
    // } else if (error.response.status === 401) {
    //   localStorage.removeItem("cabinetToken");
    //
    //   window.location.replace(
    //     process.env.VUE_APP_CABINET_APP_URL +
    //     process.env.VUE_APP_CABINET_APP_SERVICE +
    //     process.env.VUE_APP_CABINET_APP_TOKEN
    //   );
    //   //vuexStore.commit("auth/setUserData", null);
    //   //localStorage.removeItem("authToken");
    //     //router.push({ name: "Unauthorized" });
    // } else if (error.response.status === 404) {
    //     router.push({ name: "NotFoundPage" });
    // } else if(error.response.status === 500) {
    //     vuexStore.commit("setErrors", { error: error.response.data.message });
    //     console.error(error.response.data.message);
    // } else {
    //     return Promise.reject(error);
    // }
    return Promise.reject(error); //default error
  },
);
api.interceptors.request.use(
  function (config) {
    config.headers.common = {
      // Authorization: `Bearer ${localStorage.getItem("authToken")}`,
      Authorization: localStorage.getItem('cabinetToken'),
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };

    if (config.showLoader) {
      vuexStore.dispatch('loader/show');
    }

    return config;
  },
  (error) => {
    if (error.config.showLoader) {
      vuexStore.dispatch('loader/hide');
    }
    return Promise.reject(error);
  },
);

const get = async (resource, data = false, configs = null) =>
  data ? await api.get(`${resource}?${jsonToQuery(data)}`, configs) : await api.get(resource, configs);
const post = async (resource, data, configs = null) => await api.post(resource, data, configs);
const show = async (resource, id, configs = null) => await api.get(`${resource}/${id}`, configs);
const edit = async (resource, id, configs = null) => await api.get(`${resource}/${id}/edit`, configs);
const put = async (resource, data) => await api.put(resource, data);
const destroy = async (resource, id) => await api.delete(resource + '/' + id);
const patch = async (resource, id = null, data = null) =>
  id ? await api.patch(`${resource}/${id}`, data) : await api.patch(resource, data);

export default {
  get,
  post,
  put,
  destroy,
  patch,
  show,
  edit,
};
