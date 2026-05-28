<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="options"
      :server-items-length="meta.total"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }"
      class="elevation-1"
    >
      <template v-slot:item.index="{ index }">
        {{ (meta.current_page - 1) * meta.per_page + (index + 1) }}
      </template>
    </v-data-table>
  </v-container>
</template>
<script>
import api from '@/api';
import { API, ALLOWED_REQUEST_PARAMETERS } from '@/api/constants-api';

export default {
  name: 'Logs',
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'ПІБ', value: 'name', sortable: false },
        { text: 'Роль', value: 'role', sortable: false },
        { text: 'Час', value: 'created_at', sortable: false },
        { text: 'Операція', value: 'operation', sortable: false },
        { text: 'Модуль', value: 'model', sortable: false },
        { text: 'IP', value: 'ip', sortable: false },
        { text: 'data', value: 'data', sortable: false },
      ],
      items: [],
      options: {},
      meta: {},
    };
  },
  watch: {
    options: {
      handler() {
        this.getLogs();
      },
      deep: true,
    },
  },
  methods: {
    getLogs() {
      this.apiLogs().then((response) => {
        const { data, meta } = response.data;
        this.items = data;
        this.meta = meta;
      });
    },
    apiLogs() {
      const options = this.GlobalHandlingRequestParameters(ALLOWED_REQUEST_PARAMETERS.GET_LOGS, this.options);
      return api.get(API.USER_ACTIVITY, options, { showLoader: true });
    },
  },
};
</script>
