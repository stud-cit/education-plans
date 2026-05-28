<template>
  <v-container>
    <v-data-table :headers="headers" :items="items" class="elevation-1" :server-items-length="meta.total"
      :item-class="this.itemRowBackground" :options.sync="options"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }" :loading="itemsLoading">
      <template v-slot:top>
        <v-row class="px-4">
          <v-col cols="12" md="6">
            <v-autocomplete v-model="year" :items="filters.years" item-text="year" item-value="id" label="Рік"
              hide-details clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete v-model="education_program" :items="filters.education_programs" item-text="title"
              item-value="id" label="Освітня програма" hide-details clearable></v-autocomplete>
          </v-col>
        </v-row>

        <v-row class="px-4 pb-4">
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="faculty" :items="filters.faculties" item-text="name" item-value="id"
              label="Факультет" hide-details clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="department" :items="departments" item-text="name" item-value="id" label="Кафедра"
              hide-details :loading="departmentsLoading" clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="division" :items="filters.divisions" item-text="title" item-value="id" hide-details
              label="Представник відділу" clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-select v-model="verificationDivisionStatus" :items="filters.verificationsStatus"
              :disabled="division === null" item-text="title" item-value="id" select hide-details
              label="Статус верифікації" clearable></v-select>
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
        {{ (index + 1) + (options.itemsPerPage * (options.page - 1)) }}
      </template>
      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Перегляд/Редагувати">
          <router-link v-if="item.actions.preview" :to="{ name: 'CatalogEducationProgram', params: { id: item.id } }"
            target="_blank">
            <v-icon small class="mr-2" color="primary">mdi-pencil</v-icon>
          </router-link>
        </btn-tooltip>
        <btn-tooltip tooltip="Скопіювати">
          <v-icon v-if="item.actions.copy" small class="mr-1 cursor-pointer" color="primary"
            @click="openDialogCopy(item)">
            mdi-content-copy
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon v-if="item.actions.delete" small class="mr-2 cursor-pointer" color="red"
            @click="deleted(item.id, item.education_program, item.year)">
            mdi-trash-can-outline
          </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <AddButton @show="() => (this.createModal = true)">Створити каталог</AddButton>

    <createCatalogModal :dialog="createModal" @close="closeDialogCreate" @submit="store" ref="createModal"
      :object="filters" />
    <copyCatalogModal :dialog="copyModal" @close="closeDialogCopy" :item="item" @submit="copy" :object="filters"
      ref="copyModal" />
  </v-container>
</template>

<script>
import GlobalMixin from '@/mixins/GlobalMixin';
import { ALLOWED_REQUEST_PARAMETERS, API } from '@/api/constants-api';
import AddButton from '@c/base/AddButton';
import api from '@/api';
import CreateCatalogModal from '@/views/pages/SelectiveDisciplines/CatalogEducationPrograms/createCatalogModal';
import copyCatalogModal from '@/views/pages/SelectiveDisciplines/CatalogEducationPrograms/copyCatalogModal';
import RolesMixin from '@/mixins/RolesMixin';
import BackgroundRowMixin from '@/mixins/BackgroundRowMixin';

export default {
  name: 'CatalogEducationPrograms',
  components: {
    CreateCatalogModal,
    copyCatalogModal,
    AddButton,
  },
  data() {
    return {
      year: null,
      education_program: null,
      faculty: null,

      departments: [],
      department: null,
      departmentsLoading: false,

      division: null,
      verificationDivisionStatus: 1,

      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Рік', value: 'year', sortable: false },
        { text: 'Освітня програма', value: 'education_program', sortable: false },
        { text: 'Рівень освіти', value: 'education_level', sortable: false },
        { text: 'Кафедра', value: 'department', sortable: false },
        { text: 'Дії', value: 'actions', sortable: false, width: '140px' },
      ],
      items: [],
      item: null,
      itemsLoading: true,
      meta: [],
      options: null,
      createModal: false,
      copyModal: false,

      filters: {
        divisions: [],
        education_levels: [],
        faculties: [],
        education_programs: [],
        verificationsStatus: [],
        years: [],
      },
    };
  },
  mixins: [RolesMixin, BackgroundRowMixin],
  mounted() {
    this.apiGetFilters();
    const q = this.$route.query;
    if (q.year) this.year = Number(q.year);
    if (q.education_program) this.education_program = Number(q.education_program);
    if (q.faculty) this.faculty = Number(q.faculty);
    if (q.department) this.department = Number(q.department);
    if (q.division) this.division = Number(q.division);
    if (q.verificationDivisionStatus) this.verificationDivisionStatus = Number(q.verificationDivisionStatus);

    if (q.page || q.itemsPerPage) {
      this.options = {
        ...this.options,
        page: q.page ? Number(q.page) : 1,
        itemsPerPage: q.itemsPerPage ? Number(q.itemsPerPage) : 15,
      };
    }

  },
  watch: {
    faculty(v) {
      v !== null ? this.apiGetDepartments(v) : (this.departments = []);
    },
    faculties(v) {
      if (v.length === 1) {
        this.faculty = v[0].id;
      }
    },
    // options() {
    //   this.apiGetItems();
    // },
    options: {
      handler() {
        this.updateUrlParameters();
        this.apiGetItems();
      },
      deep: true
    }
  },
  methods: {
    updateUrlParameters() {
      const query = {
        year: this.year,
        education_program: this.education_program,
        faculty: this.faculty,
        department: this.department,
        division: this.division,
        verificationDivisionStatus: this.verificationDivisionStatus,
        page: this.options.page,
        itemsPerPage: this.options.itemsPerPage
      }

      Object.keys(query).forEach(key => (query[key] == null) && delete query[key]);

      this.$router.replace({ query }).catch(() => { });
    },
    async apiGetItems() {
      const options = GlobalMixin.methods.GlobalHandlingRequestParameters(
        ALLOWED_REQUEST_PARAMETERS.GET_CATALOG_EDUCATION_PROGRAMS,
        this.options,
      );
      try {
        const response = await api.get(API.CATALOG_EDUCATION_PROGRAMS, options, { showLoader: true });
        const { data } = response;
        this.itemsLoading = false;
        this.items = data.data;
        this.meta = data.meta;
      } catch (e) {
        this.itemsLoading = false;
        console.error(e); // TODO: show error
      }
    },
    apiGetFilters() {
      api.get(API.CATALOG_EDUCATION_PROGRAMS_FILTERS).then(({ data }) => {
        this.filters = data;
      });
    },
    apiGetDepartments(id) {
      this.departmentsLoading = true;

      api.show(API.DEPARTMENTS, id).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },
    store(item) {
      api
        .post(API.CATALOG_EDUCATION_PROGRAMS, item)
        .then((response) => {
          this.$refs.createModal.close();

          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });

          this.apiGetItems();
        })
        .catch((errors) => {
          this.$refs.createCatalogModal.submissionFinished();
          this.$refs.createModal.setErrors(errors.response.data.errors);
        });
    },
    copy(data) {
      api
        .patch(API.CATALOG_EDUCATION_PROGRAMS_COPY, data.catalog_id, data)
        .then((response) => {
          this.$refs.copyModal.close();
          this.item = null;

          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });

          this.apiGetItems();
        })
        .catch((errors) => {
          this.$refs.copyModal.setErrors(errors.response.data.errors);
        });
    },
    deleted(id, education_program, year) {
      this.$swal
        .fire({
          title: `Ви хочете видалити каталог?`,
          text: `За спеціальністю ${education_program} ${year} - ${year + 1}р.`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api.destroy(API.CATALOG_EDUCATION_PROGRAMS + '/delete', id).then((response) => {
              const { message, warning } = response.data;
              this.$swal.fire({
                position: 'center',
                icon: warning ? 'warning' : 'success',
                title: message,
                showConfirmButton: false,
                timer: 3000,
              });

              !warning && this.apiGetItems();
            });
          }
        });
    },
    clear() {
      this.options.page = 1;
      this.options.year = null;
      this.education_program = this.options.education_program = null;
      this.faculty = this.options.faculty = null;
      this.department = this.options.department = null;
      this.division = this.options.divisionWithStatus = null;
      this.apiGetItems();
    },
    search() {
      this.options.page = 1;
      this.options.year = this.year;
      this.options.education_program = this.education_program;
      this.options.faculty = this.faculty;
      this.options.department = this.department;
      if (this.division !== null) {
        this.options.divisionWithStatus = [this.division, this.verificationDivisionStatus];
      }
      this.updateUrlParameters();
      this.apiGetItems();
    },
    closeDialogCreate() {
      this.createModal = false;
    },
    openDialogCopy(data) {
      this.item = data;
      this.copyModal = true;
    },
    closeDialogCopy() {
      this.copyModal = false;
    },
  },
};
</script>

<style scoped></style>
