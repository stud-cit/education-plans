<template>
  <v-container>
    <v-data-table :headers="computedHeaders" :items="items" class="elevation-1" hide-default-footer disable-pagination>
      <template v-slot:item.index="{ index }">
        {{ index + 1 }}
      </template>

      <template v-slot:item.value="{ item }">
        <template v-if="item.edit">
          <validation-observer ref="observer">
            <validation-provider v-slot="{ errors }" name="Значення" rules="required|double,dot">
              <v-text-field v-model="item.value" :error-messages="errors" required type="number" min="0" step="0.01"
                autofocus dense hide-details @change="edit(item)" @blur="closeEdit(item)"></v-text-field>
            </validation-provider>
          </validation-observer>
        </template>
        <template v-else>
          {{ item.value }}
        </template>
      </template>

      <template v-slot:item.actions="{ item }">

        <btn-tooltip :tooltip="!item.edit ? 'Редагувати' : 'Зберегти'">
          <v-icon v-if="item.edit" small class="mr-2" color="success" @click="edit(item)"> mdi-check </v-icon>
          <v-icon v-if="!item.edit" small class="mr-2" color="primary" @click="item.edit = true">
            mdi-square-edit-outline
          </v-icon>
        </btn-tooltip>

        <btn-tooltip tooltip="Видалити">
          <v-icon v-if="allowedRoles([ROLES.ID.root])" small class="mr-2" color="red"
            @click="deleteItem(item.id, item.title)">
            mdi-trash-can-outline
          </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <v-tooltip left color="info" v-if="allowedRoles([ROLES.ID.root])">
      <template v-slot:activator="{ on, attrs }">
        <v-fab-transition>
          <v-btn color="primary" dark fixed bottom right fab v-bind="attrs" v-on="on" :to="{ name: 'RestrictCreate' }">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-fab-transition>
      </template>
      <span>Додати налаштування</span>
    </v-tooltip>
  </v-container>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { required, numeric, max, double } from "vee-validate/dist/rules";
import RolesMixin from '@/mixins/RolesMixin';
import { ROLES } from '@/utils/constants';
import {
  extend,
  ValidationObserver,
  ValidationProvider,
  setInteractionMode,
} from "vee-validate";

setInteractionMode("eager");

extend("double,dot", {
  ...double,
  message: "{_field_} needs to be double. ({_value_})",
});

export default {
  name: 'RestrictionEditor',

  data() {
    return {
      ROLES,
      max3digits: (v) => v.length <= 3 || 'Максимум 3 цифри!',
      items: [],
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Ключ', value: 'key', sortable: false },
        { text: 'Заголовок', value: 'title', sortable: false },
        { text: 'Значення', value: 'value', sortable: false, width: '100px' },
        { text: 'Дії', value: 'actions', sortable: false, width: '100px' },
      ],
    };
  },
  computed: {
    computedHeaders() {
      return this.headers.filter((item) => {
        return this.exceptRoles([ROLES.ID.root]) ? item.value !== 'key' : item;
      });
    },
  },
  mixins: [RolesMixin],
  created() {
    this.getSettings();
  },
  methods: {
    getSettings() {
      this.apiSettings().then((response) => {
        const { data } = response;
        this.items = data.data;
      });
    },
    apiSettings() {
      return api.get(API.SETTINGS, null, { showLoader: true });
    },
    save(id, value) {
      const options = { value: value };
      api.put(`${API.SETTINGS}${id}`, options).then((response) => {
        const { message } = response.data;
        this.snack = true;
        this.snackColor = 'success';
        this.snackText = message;
      });
    },
    closeEdit(item) {
      if (item.value === '') return;
      item.edit = false;
    },
    edit(data) {
      if (data.value === '') return;
      api.put(API.SETTINGS + '/' + data.id, { value: data.value }).then((response) => {
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

    deleteItem(id, title) {
      this.$swal
        .fire({
          title: `Ви хочете видалити налаштування?`,
          text: `${title}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            return api
              .destroy(API.SETTINGS, id)
              .then((response) => {
                const { message } = response.data;
                this.$swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: message,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })
              .then(() => this.getSettings());
          }
        });
    },
  },
};
</script>
