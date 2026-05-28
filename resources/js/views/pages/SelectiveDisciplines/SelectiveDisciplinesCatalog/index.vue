<template>
  <v-container>
    <v-data-table :headers="headers" :items="items" class="elevation-1" :item-class="this.itemRowBackground"
      :server-items-length="meta.total" :options.sync="options"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }">
      <template v-slot:top>
        <v-row class="px-4">
          <v-col cols="12" md="6">
            <v-autocomplete v-model="year" :items="years" item-text="year" item-value="id" label="Рік"
              hide-details></v-autocomplete>
          </v-col>
          <v-col cols="12" md="6">
            <v-autocomplete v-model="group" :items="groups" item-text="title" item-value="id" label="Група" hide-details
              clearable></v-autocomplete>
          </v-col>
        </v-row>

        <v-row class="px-4 pb-4">
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="faculty" :items="faculties" item-text="name" item-value="id" label="Факультет"
              :loading="facultiesLoading" hide-details clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="department" :items="departments" item-text="name" item-value="id" label="Кафедра"
              hide-details :loading="departmentsLoading" clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-autocomplete v-model="division" :items="divisions" item-text="title" item-value="id" hide-details
              label="Представник відділу" clearable></v-autocomplete>
          </v-col>
          <v-col cols="12" lg="6">
            <v-select v-model="verificationDivisionStatus" :items="verificationsDivisionsStatus"
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
      <template v-slot:item.title="{ item }">
        {{ item.title }}
      </template>
      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Перегляд">
          <v-icon v-if="item.actions.preview" small class="mr-2" color="primary" @click="openDialogPreview(item)">
            mdi-eye
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Редагувати">
          <v-icon v-if="item.actions.edit" small class="mr-2" color="primary" @click="openDialogEdit(item)">
            mdi-pencil
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon v-if="item.actions.delete" small class="mr-2" color="red" @click="deleted(item.id, item)">
            mdi-trash-can-outline
          </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <v-speed-dial v-model="nav" bottom right fixed direction="top" transition="slide-y-reverse-transition">
      <template v-slot:activator>
        <v-btn v-model="nav" color="blue darken-2" dark fab>
          <v-icon v-if="nav"> mdi-close </v-icon>
          <v-icon v-else> mdi-dots-vertical </v-icon>
        </v-btn>
      </template>
      <v-tooltip left color="info" v-if="exceptRoles([ROLES.ID.guest])">
        <template v-slot:activator="{ on, attrs }">
          <v-fab-transition>
            <v-btn color="warning" small dark fab v-bind="attrs" v-on="on" @click="openDialogCatalog">
              <v-icon>mdi-cog-outline</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>Налаштування каталогів</span>
      </v-tooltip>
      <v-tooltip left color="info" v-if="options && options.year && options.group">
        <template v-slot:activator="{ on, attrs }">
          <v-fab-transition>
            <v-btn fab dark small color="red accent-4" v-bind="attrs" v-on="on" @click="openDialogPdf">
              <v-icon>mdi-pdf-box</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>PDF</span>
      </v-tooltip>
      <v-tooltip left color="info">
        <template v-slot:activator="{ on, attrs }">
          <v-fab-transition>
            <v-btn color="primary" small dark fab v-bind="attrs" v-on="on" @click="openDialogCreate">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>Додати дисципліну</span>
      </v-tooltip>
    </v-speed-dial>

    <CreateSelectiveDisciplinesCatalogModal :dialog="createModal" @close="closeDialogCreate" @submit="create"
      ref="createModal" />
    <EditSelectiveDisciplinesCatalogModal :dialog="editModal" :item="item" @close="closeDialogEdit" @submit="edit"
      ref="editModal" />
    <PreviewSelectiveDisciplinesCatalogModal :dialog="previewModal" :item="item" @close="closeDialogPreview"
      @init="apiGetItems" ref="previewModal" />
    <PdfSelectiveDisciplinesCatalogModal :dialog="pdfModal" @close="closeDialogPdf" :options="options" ref="pdfModal" />
    <CatalogSelectiveDisciplinesCatalogModal :dialog="catalogModal" @close="closeDialogCatalog" ref="catalogModal" />
  </v-container>
</template>

<script>
import api from '@/api';
import { ALLOWED_REQUEST_PARAMETERS, API } from '@/api/constants-api';
import { ROLES } from '@/utils/constants';
import GlobalMixin from '@/mixins/GlobalMixin';
import RolesMixin from '@/mixins/RolesMixin';
import BackgroundRowMixin from '@/mixins/BackgroundRowMixin';
import PreviewSelectiveDisciplinesCatalogModal from '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog/previewModal';
import CreateSelectiveDisciplinesCatalogModal from '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog/createModal';
import EditSelectiveDisciplinesCatalogModal from '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog/editModal';
import PdfSelectiveDisciplinesCatalogModal from '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog/pdfModal';
import CatalogSelectiveDisciplinesCatalogModal from '@/views/pages/SelectiveDisciplines/SelectiveDisciplinesCatalog/catalogModal';
import { mapGetters } from 'vuex';
export default {
  name: 'SelectiveDisciplinesCatalog',
  components: {
    PdfSelectiveDisciplinesCatalogModal,
    EditSelectiveDisciplinesCatalogModal,
    CreateSelectiveDisciplinesCatalogModal,
    PreviewSelectiveDisciplinesCatalogModal,
    CatalogSelectiveDisciplinesCatalogModal,
  },
  data() {
    return {
      nav: false,
      years: [],
      year: new Date().getFullYear(),

      groups: [],
      group: null,

      faculties: [],
      faculty: null,
      facultiesLoading: false,

      departments: [],
      department: null,
      departmentsLoading: false,

      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Рік', value: 'year', sortable: false },
        { text: 'Назва дисципліни', value: 'title', sortable: false },
        { text: 'Група', value: 'group', sortable: false },
        { text: 'Кафедра', value: 'department', sortable: false },
        { text: 'Дата оновлення', value: 'updated_at', sortable: false, width: '150px' },
        { text: 'Дії', value: 'actions', sortable: false, width: '120px' },
      ],
      items: [],
      item: null,
      meta: [],
      options: null,
      divisions: [],
      division: null,
      verificationsDivisionsStatus: [],
      verificationDivisionStatus: 1,

      previewModal: false,
      createModal: false,
      editModal: false,
      pdfModal: false,
      catalogModal: false,
      ROLES,
    };
  },
  mounted() {
    this.apiGetYears();
    this.apiGetDivisions();
    this.apiGetGroups();
    this.apiGetFaculties();
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
  },
  mixins: [RolesMixin, BackgroundRowMixin],
  watch: {
    faculty(v) {
      v !== null ? this.apiGetDepartments(v) : (this.departments = []);
    },
    faculties(v) {
      if (v.length === 1) {
        this.faculty = v[0].id;
      }
    },
    year(v) {
      this.options.year = v;
    },
    options(v) {
      const cYear = new Date().getFullYear();
      v.year = cYear === this.year ? cYear : this.year;
      this.apiGetItems();
    },
  },
  methods: {
    async apiGetItems() {
      const options = GlobalMixin.methods.GlobalHandlingRequestParameters(
        ALLOWED_REQUEST_PARAMETERS.GET_CATALOG_SELECTIVE_SUBJECTS,
        this.options,
      );

      try {
        const response = await api.get(API.CATALOG_SELECTIVE_SUBJECTS, options, { showLoader: true });
        const { data } = response;
        this.items = data.data;
        this.meta = data.meta;
      } catch (e) {
        console.error(e); // TODO: show error
      }
    },
    apiGetFaculties() {
      api.get(API.FACULTIES).then(({ data }) => {
        this.faculties = data.data;
        this.facultiesLoading = false;
      });
    },
    apiGetDepartments(id) {
      this.departmentsLoading = true;

      api.show(API.DEPARTMENTS, id).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },
    apiGetGroups() {
      api.get(API.CATALOG_GROUPS + '/list').then(({ data }) => {
        this.groups = data.data;
      });
    },
    async apiGetYears() {
      const response = await api.get(API.CATALOG_SUBJECTS + '/years');
      const { data } = response.data;
      this.years = data;
    },
    create(data) {
      api
        .post(API.CATALOG_SELECTIVE_SUBJECTS, data)
        .then((response) => {
          this.createModal = false;

          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });

          this.apiGetItems();
          this.$refs.createModal.clear();
        })
        .catch((errors) => {
          this.$refs.createModal.setErrors(errors.response.data.errors);
        });
    },
    apiGetDivisions() {
      api.get(API.CATALOG_SELECTIVE_SUBJECTS_FILTERS).then(({ data }) => {
        this.divisions = data.divisions;
        this.verificationsDivisionsStatus = data.verificationsStatus;
        this.faculties = data.faculties;
      });
    },
    clear() {
      this.options.page = 1;
      this.options.year = new Date().getFullYear();
      this.group = this.options.group = null;
      this.faculty = this.options.faculty = null;
      this.department = this.options.department = null;
      this.division = this.options.divisionWithStatus = null;
      this.apiGetItems();
    },
    search() {
      this.options.page = 1;
      this.options.year = this.year;
      this.options.group = this.group;
      this.options.faculty = this.faculty;
      this.options.department = this.department;
      if (this.division !== null) {
        this.options.divisionWithStatus = [this.division, this.verificationDivisionStatus];
      }
      this.apiGetItems();
    },
    edit(data) {
      api
        .patch(API.CATALOG_SELECTIVE_SUBJECTS, data.id, data)
        .then((response) => {
          this.editModal = false;

          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
          this.apiGetItems();
          this.$refs.editModal.clear();
        })
        .catch((errors) => {
          this.$refs.editModal.setErrors(errors.response.data.errors);
        });
    },
    deleted(id, item) {
      const text = '<h4>' + item.title + '</h4>';
      this.$swal
        .fire({
          title: `Ви хочете видалити дисципліну?`,
          html: `${text}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api.destroy(API.CATALOG_SELECTIVE_SUBJECTS, id).then((response) => {
              const { message } = response.data;
              this.$swal.fire({
                position: 'center',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 1500,
              });
              this.apiGetItems();
            });
          }
        });
    },
    openDialogPreview(item) {
      this.item = item;
      this.previewModal = true;
    },
    closeDialogPreview() {
      this.previewModal = false;
      this.item = null;
    },
    openDialogCreate() {
      this.createModal = true;
    },
    closeDialogCreate() {
      this.createModal = false;
    },
    openDialogEdit(item) {
      this.item = item;
      this.editModal = true;
    },
    closeDialogEdit() {
      this.editModal = false;
    },
    openDialogPdf() {
      this.pdfModal = true;
    },
    closeDialogPdf() {
      this.pdfModal = false;
    },
    openDialogCatalog() {
      this.catalogModal = true;
    },
    closeDialogCatalog() {
      this.apiGetYears();
      this.catalogModal = false;
    },
  },
};
</script>

<style scoped></style>
