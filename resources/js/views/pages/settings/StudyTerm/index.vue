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
      <span>Створити термін навчання</span>
    </v-tooltip>

    <CreateStudyTermModal
      :dialog="showCreate"
      @close="
        () => {
          this.showCreate = false;
        }
      "
      @submit="create"
    />

    <EditStudyTermModal
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
import CreateStudyTermModal from '@/views/pages/settings/StudyTerm/create';
import EditStudyTermModal from '@/views/pages/settings/StudyTerm/edit';

export default {
  name: 'StudyTerm',
  components: {
    CreateStudyTermModal,
    EditStudyTermModal,
  },
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Опис', value: 'title', sortable: false },
        { text: 'Років', value: 'year', sortable: false },
        { text: 'Місяців', value: 'month', sortable: false },
        { text: 'Курсів', value: 'course', sortable: false },
        { text: 'Модулів', value: 'module', sortable: false },
        { text: 'Семестрів', value: 'semesters', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      items: [],
      item: {},
      showCreate: false,
      showEdit: false,
    };
  },
  created() {
    this.getStudyTerms();
  },
  methods: {
    getStudyTerms() {
      this.apiStudyTerms().then((response) => {
        const { data } = response;
        this.items = data.data;
      });
    },
    apiStudyTerms() {
      return api.get(`${API.STUDY_TERMS}/select`, null, { showLoader: true });
    },
    openEdit(item) {
      const { id, title, year, month, course, module, semesters } = item;
      this.showEdit = true;
      this.item = { id, title, year, month, course, module, semesters };
    },
    edit(item) {
      let { id, title, year, month, course, module, semesters } = item;
      const data = { id, title, year, month, course, module, semesters };
      this.closeEdit();
      api
        .put(`${API.STUDY_TERMS}/${id}`, data)
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
        .then(() => this.getStudyTerms());
    },
    closeEdit() {
      this.showEdit = false;
    },
    closeCreate() {
      this.showCreate = false;
    },
    create(item) {
      api
        .post(API.STUDY_TERMS, item, { showLoader: true })
        .then((response) => {
          this.closeCreate();
          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
        })
        .then(() => this.getStudyTerms());
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
              .destroy(API.STUDY_TERMS, id)
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
              .then(() => this.getStudyTerms());
          }
        });
    },
  },
};
</script>
