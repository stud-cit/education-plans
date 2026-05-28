<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :loading="loader"
      loading-text="Завантаження... Будь ласка зачекайте"
      class="elevation-1"
      hide-default-footer
      disable-pagination
    >
      <template v-slot:item.index="{ index }">
        {{ ++index }}
      </template>

      <template v-slot:item.agreed="{ item }">
        <v-checkbox v-model="item.agreed" @click="edit(item)"></v-checkbox>
      </template>

      <template v-slot:item.position="{ item }">
        <template v-if="item.edit">
          <validation-observer ref="observer">
            <validation-provider v-slot="{ errors }" name="Посада" rules="required|max:255">
              <v-text-field
                v-model="item.position"
                :counter="255"
                :error-messages="errors"
                label="Назва"
                required
                autofocus
                @change="edit(item)"
                @blur="closeEdit(item)"
              ></v-text-field>
            </validation-provider>
          </validation-observer>
        </template>
        <template v-else>
          {{ item.position }}
        </template>
      </template>

      <template v-slot:item.actions="{ item }">
        <btn-tooltip v-if="!item.edit" tooltip="Редагувати">
          <v-icon small class="mr-2" color="primary" @click="item.edit = true"> mdi-square-edit-outline </v-icon>
        </btn-tooltip>
        <btn-tooltip v-else tooltip="Зберегти">
          <v-icon small class="mr-2" color="success" @click="edit(item)"> mdi-check </v-icon>
        </btn-tooltip>

        <btn-tooltip tooltip="Видалити">
          <v-icon small class="mr-2" color="red" @click="deleted(item.id, item.position)"> mdi-trash-can-outline </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <v-tooltip left color="info">
      <template v-slot:activator="{ on, attrs }">
        <v-fab-transition>
          <v-btn color="primary" dark fixed bottom right fab v-bind="attrs" v-on="on" @click="showCreate = true">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-fab-transition>
      </template>
      <span>Додати посаду</span>
    </v-tooltip>

    <CreatePositionModal
      :dialog="showCreate"
      @close="
        () => {
          this.showCreate = false;
        }
      "
      @submit="create"
    />
  </v-container>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import CreatePositionModal from '@/views/pages/settings/Position/create';

export default {
  name: 'Position',
  components: { CreatePositionModal },
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Посада', value: 'position', sortable: false },
        { text: 'Погоджено', value: 'agreed', sortable: false, width: '20px' },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      items: [],
      loader: true,
      showCreate: false,
    };
  },
  mounted() {
    this.getPositions();
  },
  methods: {
    getPositions() {
      this.apiPositions().then((response) => {
        const { data } = response;
        this.items = data.data;
        this.loader = false;
      });
    },
    apiPositions() {
      return api.get(API.POSITIONS);
    },
    edit(data) {
      let { id, position, agreed } = data;
      if (position === '') return;
      api.put(`${API.POSITIONS}/${id}`, { position, agreed }).then((response) => {
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
    closeEdit(item) {
      if (item.position === '') return;
      item.edit = false;
    },
    closeCreate() {
      this.showCreate = false;
    },
    create(position) {
      api
        .post(API.POSITIONS, position)
        .then((response) => {
          this.showCreate = false;
          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        })
        .then(() => this.getPositions());
    },
    deleted(id, position) {
      this.$swal
        .fire({
          title: `Ви хочете видалити посаду?`,
          text: `${position}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api
              .destroy(API.POSITIONS, id)
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
              .then(() => this.getPositions());
          }
        });
    },
  },
};
</script>
