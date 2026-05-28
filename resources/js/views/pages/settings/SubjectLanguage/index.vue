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
          <v-icon small class="mr-2" color="red" @click="deleteItem(item.id, item.title)"> mdi-trash-can-outline </v-icon>
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
      <span>Додати мову</span>
    </v-tooltip>

    <CreateSubjectLanguageModal
      :dialog="showCreate"
      @close="
        () => {
          this.showCreate = false;
        }
      "
      @submit="create"
    />

    <EditSubjectLanguageModal
      :dialog="showEdit"
      :item="item"
      @close="
        () => {
          this.showEdit = false;
        }
      "
      @submit="edit"
    />
  </v-container>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import vuexStore from '@/store';
import CreateSubjectLanguageModal from '@/views/pages/settings/SubjectLanguage/create';
import EditSubjectLanguageModal from '@/views/pages/settings/SubjectLanguage/edit';

export default {
  name: 'SubjectLanguage',
  components: {
    CreateSubjectLanguageModal,
    EditSubjectLanguageModal,
  },
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Мова', value: 'title', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      items: [],
      showCreate: false,
      showEdit: false,
      item: {},
    };
  },
  created() {
    this.getData();
  },
  methods: {
    async getData() {
      try {
        const response = await api.get(API.SUBJECT_LANGUAGES, null, { showLoader: true });
        this.items = response.data.data;
      } catch (error) {
        vuexStore.dispatch('loader/hide');
        vuexStore.commit('setErrors', { error: 'Server error' });
      }
    },
    closeCreate() {
      this.showCreate = false;
    },
    create(language) {
      api
        .post(API.SUBJECT_LANGUAGES, language)
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
        .then(() => this.getData());
    },
    openEdit(item) {
      const { id, title } = item;
      this.showEdit = true;
      this.item = { id, title };
    },
    edit(data) {
      let { id, title } = data;
      if (title === '') return;

      api
        .put(`${API.SUBJECT_LANGUAGES}/${id}`, { title })
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
        .then(() => this.getData());
    },
    closeEdit(item) {
      if (item.title === '') return;
      item.edit = false;
    },
    deleteItem(id, title) {
      this.$swal
        .fire({
          title: `Ви справді хочете видалити ?`,
          text: `${title}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api
              .destroy(API.SUBJECT_LANGUAGES, id)
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
              .then(() => this.getData());
          }
        });
    },
  },
};
</script>

<style scoped></style>
