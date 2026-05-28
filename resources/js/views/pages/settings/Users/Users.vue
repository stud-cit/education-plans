<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :options.sync="options"
      :server-items-length="meta.total"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }"
      class="elevation-1"
      :loading="itemsLoading"
    >
      <template v-slot:top>
        <v-row class="px-4">
          <v-col cols="12" md="6">
            <v-text-field v-model="worker" label="Пошук по ПІБ"></v-text-field>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="role"
              :items="roles"
              item-text="label"
              item-value="id"
              label="Роль"
              hide-details
              clearable
            ></v-autocomplete>
          </v-col>
        </v-row>

        <v-row class="px-4 pb-4">
          <v-col cols="12" lg="6">
            <v-autocomplete
              v-model="faculty"
              :items="faculties"
              item-text="name"
              item-value="id"
              label="Факультет"
              :loading="facultiesLoading"
              hide-details
              clearable
            ></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-autocomplete
              v-model="department"
              :items="departments"
              item-text="name"
              item-value="id"
              label="Кафедра"
              hide-details
              :loading="departmentsLoading"
              clearable
            ></v-autocomplete>
          </v-col>
        </v-row>
        <v-row class="px-4 pb-4">
          <v-col align-self="center" class="d-flex">
            <v-spacer></v-spacer>
            <v-btn color="primary" outlined @click="search"> Пошук </v-btn>
            <v-btn color="primary" class="ml-2" outlined @click="clear"> Очистити </v-btn>
          </v-col>
        </v-row>
      </template>
      <template v-slot:item.index="{ index }">
        {{ (meta.current_page - 1) * meta.per_page + (index + 1) }}
      </template>
      <template v-slot:item.role="{ item }">
        <template v-if="roles.length">
          {{ roles.find((el) => el.id === item.role_id).label }}
        </template>
      </template>

      <template v-slot:item.actions="{ item }">

        <btn-tooltip tooltip="Редагувати">
          <v-icon small class="mr-2" color="primary" @click="openEdit(item)"> mdi-pencil </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon small class="mr-2" color="red" @click="deleted(item.id, item.full_name)">
            mdi-trash-can-outline
          </v-icon>
        </btn-tooltip>


      </template>
    </v-data-table>

    <v-tooltip left color="info">
      <template v-slot:activator="{ on, attrs }">
        <v-fab-transition>
          <v-btn color="primary" dark fixed bottom right fab v-bind="attrs" v-on="on" @click="showCreateOrEditDialog = true">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-fab-transition>
      </template>
      <span>Додати користувача</span>
    </v-tooltip>

    <CreateOrUpdateUserModal
      :dialog="showCreateOrEditDialog"
      :workers="workers"
      :item="user"
      :roles="roles"
      :faculties="faculties"
      @close="closeDialog"
      @store="create"
      @update="update"
      ref="createOrUpdateDialog"
    />
  </v-container>
</template>

<script>
import api from '@/api';
import { API, ALLOWED_REQUEST_PARAMETERS } from '@/api/constants-api';
import CreateOrUpdateUserModal from '@/views/pages/settings/Users/Modals/CreateOrUpdate';

export default {
  name: 'Users',
  components: { CreateOrUpdateUserModal },
  data() {
    return {
      items: [],
      itemsLoading: true,
      roles: [],
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'ПІБ', value: 'full_name', sortable: false },
        { text: 'Факультет', value: 'faculty', sortable: false },
        { text: 'Кафедра', value: 'department', sortable: false },
        { text: 'Роль', value: 'role', width: '200px', sortable: false },
        { text: 'Дії', value: 'actions', width: '80px', sortable: false },
      ],
      showCreateOrEditDialog: false,
      user: null,
      workers: [],
      meta: {},
      options: {},
      worker: null,
      role: null,
      faculty: null,
      faculties: [],
      department: null,
      departments: [],
      departmentsLoading: false,
      facultiesLoading: true,
    };
  },
  mounted() {
    this.apiGetRoles();
    this.apiWorkers();
    this.apiGetFaculties();
  },
  watch: {
    options: {
      handler() {
        this.apiUsers();
      },
    },
    'faculty'(v) {
      v ? this.apiGetDepartments(v) : (this.departments = []);
    },
  },
  methods: {
    async apiUsers() {
      const options = this.GlobalHandlingRequestParameters(ALLOWED_REQUEST_PARAMETERS.GET_USERS, this.options);
      const {
        data: { data, meta },
      } = await api.get(API.USERS, options, { showLoader: true });
      this.items = data;
      this.itemsLoading = false;
      this.meta = meta;
    },
    apiWorkers() {
      api.get(API.WORKERS).then((response) => {
        const { data } = response;
        this.workers = data;
      });
    },
    apiGetFaculties() {
      api.get(API.FACULTIES).then(({ data }) => {
        this.faculties = data.data;
        this.facultiesLoading = false;
      });
    },
    apiGetDepartments(id) {
      this.departmentsLoading = true;

      api.show(API.DEPARTMENTS, id).then(({data}) => {
        this.departments = data.data
        this.departmentsLoading = false;
      })
    },
    async apiGetRoles() {
      const { data } = await api.get(API.ROLES, null, { showLoader: true });
      this.roles = data.data;
    },
    closeDialog() {
      this.user = null;
      this.showCreateOrEditDialog = false;
      this.$refs.createOrUpdateDialog.clear();
    },
    openEdit(item) {
      this.user = item;
      this.showCreateOrEditDialog = true;
    },
    create(data) {
      api
        .post(API.USERS, data)
        .then((response) => {
          this.closeDialog();
          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });

          this.apiUsers();
          this.apiWorkers();
        })
        .catch((errors) => {
          this.$refs.createOrUpdateDialog.setErrors(errors.response.data.errors);
        });
    },
    update(data) {
      api.put(API.USERS + '/' + data.id, { ...data }).then((response) => {
        this.closeDialog();

        const { message } = response.data;
        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });

        this.apiUsers();
        this.apiWorkers();
      });
    },
    deleted(id, full_name) {
      this.$swal
        .fire({
          title: `Ви хочете видалити користувача ?`,
          text: `${full_name}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api.destroy(API.USERS, id).then((response) => {
              const { message } = response.data;
              this.$swal.fire({
                position: 'center',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 1500,
              });

              this.apiUsers();
              this.apiWorkers();
            });
          }
        });
    },
    clear() {
      this.options.worker = '';
      this.options.role = '';
      this.options.faculty = '';
      this.options.department = '';
      this.apiUsers();
    },
    search() {
      this.options.worker = this.worker;
      this.options.role = this.role;
      this.options.faculty = this.faculty;
      this.options.department = this.department;
      this.apiUsers();
    },
  },
};
</script>
