<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="options"
      :server-items-length="meta.total"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }"
      class="elevation-1"
      :item-class="itemRowBackground"
    >
      <template v-slot:item.index="{ index }">
        {{ (meta.current_page - 1) * meta.per_page + (index + 1) }}
      </template>

      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Редагувати">
          <v-icon v-if="!item.disabled" small color="primary" class="mr-2" aria-label="Редагувати запис" @click="openEdit(item)">
            mdi-pencil
          </v-icon>
        </btn-tooltip>
        <btn-tooltip v-if="!item.disabled" tooltip="Видалити">
          <v-icon
            small
            class="mr-2"
            aria-label="Архівувати запис"
            color="red"
            @click="deleteItem(item.id, item.title)"
          >
            mdi-archive-arrow-down-outline
          </v-icon>
        </btn-tooltip>
        <btn-tooltip  v-else tooltip="Відновити">
          <v-icon
            small
            class="mr-2"
            aria-label="Відновити запис"
            color="green"
            @click="restoreItem(item.id, item.title)"
          >
            mdi-archive-arrow-up-outline
          </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <AddButton @show="openCreate">Додати рівень знань</AddButton>

    <CreateEducationLevelModal
      :dialog="showCreate"
      @close="
        () => {
          this.showCreate = false;
        }
      "
      @submit="create"
    />

    <EditEducationLevelModal
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
import { API, ALLOWED_REQUEST_PARAMETERS } from '@/api/constants-api';
import vuexStore from '@/store';
import CreateEducationLevelModal from '@/views/pages/settings/EducationLevel/create';
import EditEducationLevelModal from '@/views/pages/settings/EducationLevel/edit';
import AddButton from '@/components/base/AddButton';

export default {
  name: 'EducationLevel',
  components: {
    AddButton,
    CreateEducationLevelModal,
    EditEducationLevelModal,
  },
  data() {
    return {
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Рівень знань', value: 'title', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      items: [],
      options: {},
      meta: {},
      showCreate: false,
      showEdit: false,
      item: {},
    };
  },
  watch: {
    options: {
      handler() {
        this.getData();
      },
      deep: true,
    },
  },
  methods: {
    itemRowBackground(item) {
      return item.disabled && 'red lighten-5';
    },
    async getData() {
      try {
        const options = this.GlobalHandlingRequestParameters(
          ALLOWED_REQUEST_PARAMETERS.GET_EDUCATION_LEVEL,
          this.options,
        );
        const response = await api.get(API.EDUCATION_LEVEL, options, { showLoader: true });
        this.items = response.data.data;
        this.meta = response.data.meta;
      } catch (error) {
        vuexStore.dispatch('loader/hide');
        vuexStore.commit('setErrors', { error: 'Server error' });
      }
    },
    openCreate() {
      this.showCreate = true;
    },
    closeCreate() {
      this.showCreate = false;
    },
    create(item) {
      api
        .post(API.EDUCATION_LEVEL, item)
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
        .put(`${API.EDUCATION_LEVEL}/${id}`, { title })
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
          title: `Ви справді хочете архівувати ?`,
          text: `${title}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api
              .destroy(API.EDUCATION_LEVEL, id)
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
    restoreItem(id, title) {
      this.$swal
        .fire({
          title: `Ви справді хочете відновити запис з архіву ?`,
          text: `${title}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api
              .patch(API.EDUCATION_LEVEL_RESTORE, id)
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
