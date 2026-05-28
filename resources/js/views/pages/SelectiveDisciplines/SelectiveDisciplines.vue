<template>
  <v-container>
    <v-data-table :headers="computedHeaders" :items="items" class="table-row-pointer elevation-1" hide-default-footer
      @click:row="goTo">
      <template v-slot:item.index="{ index }">
        {{ index + 1 }}
      </template>
      <template v-slot:item.title="{ item }">
        <template v-if="item.edit">
          <validation-observer ref="observer">
            <validation-provider v-slot="{ errors }" name="Назва" rules="required|max:150">
              <v-text-field v-model="item.title" :counter="150" :error-messages="errors" required autofocus
                @change="edit(item)" @blur="closeEdit(item)"></v-text-field>
            </validation-provider>
          </validation-observer>
        </template>
        <template v-else>
          {{ item.title }}
        </template>
      </template>

      <template v-slot:item.actions="{ item }">
        <!--        <v-icon v-if="item.edit" small class="mr-2" color="success" @click="edit(item)"> mdi-check </v-icon>-->
        <!--        <v-icon v-if="!item.edit" small class="mr-2" color="primary" @click="item.edit = true">-->
        <!--          mdi-square-edit-outline-->
        <!--        </v-icon>-->
        <btn-tooltip tooltip="Перегляд">
          <v-icon small class="mr-2 cursor-pointer" color="primary"> mdi-eye </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: 'SelectiveDisciplines',
  mixins: [RolesMixin],
  data() {
    return {
      items: [],
      headers: [
        { text: '№', value: 'index', width: '20px', sortable: false },
        { text: 'Назва', value: 'title', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      links: {
        1: 'SelectiveDisciplinesCatalog',
        2: 'CatalogSpecialties',
        3: 'CatalogEducationPrograms',
      },
    };
  },
  computed: {
    computedHeaders() {
      return this.headers.filter((item) => {
        return this.exceptRoles([ROLES.ID.root, ROLES.ID.admin]) ? item.value !== 'actions' : item;
      });
    },
  },
  mounted() {
    this.apiSelectiveDisciplines();
  },
  methods: {
    apiSelectiveDisciplines() {
      api.get(API.SELECTIVE_DISCIPLINES, null, { showLoader: true }).then((response) => {
        const { data } = response;
        this.items = data.data;
      });
    },
    closeEdit(item) {
      if (item.title === '') return;
      item.edit = false;
    },
    edit(data) {
      if (data.title === '') return;
      api.put(API.SELECTIVE_DISCIPLINES + '/' + data.id, { title: data.title }).then((response) => {
        data.edit = false;
        const { message } = response.data;
        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });
      });
    },
    goTo(row) {
      const { id } = row;
      this.$router.push({ name: this.links[id] });
    },
  },
};
</script>
