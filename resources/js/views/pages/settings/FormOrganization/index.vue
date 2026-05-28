<template>
  <v-container>
    <v-data-table :headers="headers" :items="items" class="elevation-1" hide-default-footer disable-pagination>
      <template v-slot:item.index="{ index }">
        {{ ++index }}
      </template>
    </v-data-table>
  </v-container>
</template>
<script>
import api from '@/api';
import { API } from '@/api/constants-api';

export default {
  name: 'FormOrganization',
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Форма організації навчання', value: 'title', sortable: false },
      ],
      items: [],
    };
  },
  created() {
    this.getFormOrganization();
  },
  methods: {
    getFormOrganization() {
      this.apiFormOrganization().then((response) => {
        const { data } = response;
        this.items = data.data;
      });
    },
    apiFormOrganization() {
      return api.get(API.FORM_ORGANIZATIONS, null, { showLoader: true });
    },
  },
};
</script>
