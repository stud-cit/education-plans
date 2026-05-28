<template>
  <v-container>
    <v-data-table :headers="headers" :items="items" class="elevation-1" hide-default-footer disable-pagination>
      <template v-slot:item.index="{ index }">
        {{ ++index }}
      </template>

      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Редагувати">
          <v-icon small class="mr-2" color="primary" @click="openEdit(item)"> mdi-pencil </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon small class="mr-2" color="red" @click="deleteItem(item.id)"> mdi-trash-can-outline </v-icon>
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
      <span>Додати</span>
    </v-tooltip>

    <CreateNoteModal :dialog="showCreate" @close="
      () => {
        this.showCreate = false;
      }
    " @submit="create" />

    <EditNoteModal :dialog="showEdit" :item="item" @close="
      () => {
        this.showEdit = false;
      }
    " @submit="edit" />
  </v-container>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import CreateNoteModal from '@/views/pages/settings/Note/create';
import EditNoteModal from '@/views/pages/settings/Note/edit';

export default {
  name: 'Note',
  components: { CreateNoteModal, EditNoteModal },
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Абревіатура', value: 'abbreviation', sortable: false, width: '110px' },
        { text: 'Опис', value: 'explanation', sortable: false },
        { text: 'Дата', value: 'date', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      items: [],
      item: {},
      showCreate: false,
      showEdit: false,
    };
  },
  mounted() {
    this.getNotes();
  },
  methods: {
    getNotes() {
      this.apiPNotes().then((response) => {
        const { data } = response;
        this.items = data.data;
      });
    },
    apiPNotes() {
      return api.get(API.NOTES, null, { showLoader: true });
    },
    openEdit(item) {
      const { id, abbreviation, explanation, date } = item;
      this.showEdit = true;
      this.item = { id, abbreviation, explanation, date };
    },
    edit(data) {
      let { id, abbreviation, explanation, date } = data;

      if (abbreviation === '' && explanation === '') return;

      api
        .put(`${API.NOTES}/${id}`, { abbreviation, explanation, date })
        .then((response) => {
          this.showEdit = false;
          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        })
        .then(() => this.getNotes());
    },
    closeEdit(item) {
      if (item.abbreviation === '' && item.explanation === '') return;
      item.edit = false;
    },
    closeCreate() {
      this.showCreate = false;
    },
    create(note) {
      api
        .post(API.NOTES, note)
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
        .then(() => this.getNotes());
    },
    deleteItem(id) {
      this.$swal
        .fire({
          title: `Ви хочете видалити?`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api
              .destroy(API.NOTES, id)
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
              .then(() => this.getNotes());
          }
        });
    },
  },
};
</script>
