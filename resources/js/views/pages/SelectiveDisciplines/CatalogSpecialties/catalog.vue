<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      class="elevation-1"
      :server-items-length="meta.total"
      :options.sync="options"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }"
    >
      <template v-slot:top>
        <v-row class="px-4 pt-3" v-if="catalog">
          <v-col>
            <p class="text-h6 mb-0 text-center" v-html="catalog.title"></p>
          </v-col>
        </v-row>
        <v-row v-if="catalog">
          <v-col cols="12">
            <verifications
              v-if="items.length"
              :item="catalog"
              :verifications-list="verificationsList"
              @submit="apiSetVerification"
              @toggleVerification="apiSetToggleToVerification"
            />
          </v-col>
        </v-row>
        <v-row class="px-4 pb-4">
          <v-col cols="12" lg="6">
            <v-autocomplete
              v-model="faculty"
              :items="faculties"
              :loading="facultiesLoading"
              item-text="name"
              item-value="id"
              label="Факультет що пропонує"
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
              label="Кафедра що пропонує"
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
        {{ ++index }}
      </template>
      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Перегляд">
          <v-icon
            v-if="item.actions.preview"
            small
            class="mr-2 cursor-pointer"
            color="primary"
            aria-label="Переглянути каталог"
            @click="openDialogPreview(item)"
          >
            mdi-eye
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Редагувати">
          <v-icon
            v-if="item.actions.edit"
            small
            class="mr-2 cursor-pointer"
            color="primary"
            aria-label="Редагувати каталог"
            @click="openDialogEdit(item)"
          >
            mdi-pencil
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon
            v-if="item.actions.delete"
            small
            class="mr-2 cursor-pointer"
            color="red"
            aria-label="Видалити каталог"
            @click="deleted(item.id, item.title)"
          >
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
      <v-tooltip left color="info" v-if="catalog && catalog.can_setting">
        <template v-slot:activator="{ on, attrs }">
          <v-fab-transition>
            <v-btn color="warning" small dark fab v-bind="attrs" v-on="on" @click="() => (settingModal = true)">
              <v-icon>mdi-cog-outline</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>Налаштування каталога</span>
      </v-tooltip>
      <v-tooltip left color="info">
        <template v-slot:activator="{ on, attrs }">
          <v-fab-transition>
            <v-btn fab dark small color="red accent-4" v-bind="attrs" v-on="on" @click="openDialogPdf">
              <v-icon>mdi-pdf-box</v-icon>
            </v-btn>
          </v-fab-transition>
        </template>
        <span>PDF</span>
      </v-tooltip>
      <v-tooltip left color="info" v-if="catalog && catalog.can_create">
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

    <PreviewSpecialitySubjectModal
      :dialog="previewModal"
      :item="item"
      :catalog="catalog"
      @close="closeDialogPreview"
      ref="previewModal"
    />
    <CreateSpecialitySubjectModal
      :dialog="createModal"
      :catalog="catalog"
      @close="closeDialogCreate"
      @submit="store"
      ref="createModal"
    />
    <EditSpecialitySubjectModal
      :dialog="editModal"
      :item="item"
      @close="closeDialogEdit"
      @submit="edit"
      ref="editModal"
    />

    <SettingCatalogModal
      :dialog="settingModal"
      :catalog="catalog"
      @close="closeDialogSetting"
      ref="settingModal"
      @init="apiGetItems"
    />

    <PdfCatalogModal :dialog="pdfModal" @close="closeDialogPdf" :options="options" :catalog="catalog" ref="pdfModal" />
  </v-container>
</template>

<script>
import GlobalMixin from '@/mixins/GlobalMixin';
import { ALLOWED_REQUEST_PARAMETERS, API } from '@/api/constants-api';
import api from '@/api';
import CreateSpecialitySubjectModal from '@/views/pages/SelectiveDisciplines/CatalogSpecialties/createSubjectModal';
import PreviewSpecialitySubjectModal from '@/views/pages/SelectiveDisciplines/CatalogSpecialties/previewSubjectModal';
import EditSpecialitySubjectModal from '@/views/pages/SelectiveDisciplines/CatalogSpecialties/editSubjectModal';
import SettingCatalogModal from '@/views/pages/SelectiveDisciplines/CatalogSpecialties/settingCatalogModal';
import PdfCatalogModal from '@/views/pages/SelectiveDisciplines/CatalogSpecialties/pdfCatalogModal';
import Verifications from '@c/base/verifications';

export default {
  name: 'CatalogSpecialty',
  components: {
    Verifications,
    PdfCatalogModal,
    CreateSpecialitySubjectModal,
    PreviewSpecialitySubjectModal,
    EditSpecialitySubjectModal,
    SettingCatalogModal,
  },
  data() {
    return {
      nav: false,
      faculty: null,
      faculties: [],
      facultiesLoading: false,

      departments: [],
      department: null,
      departmentsLoading: false,

      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Назва предмету', value: 'title', sortable: false },
        { text: 'Кафедра що пропонує', value: 'department', sortable: false },
        { text: 'Дії', value: 'actions', sortable: false, width: '140px' },
      ],
      items: [],
      item: null,
      meta: [],
      options: null,
      createModal: false,
      previewModal: false,
      editModal: false,
      settingModal: false,
      pdfModal: false,
      catalog: null,
      verificationsList: null,
    };
  },
  watch: {
    faculty(v) {
      v !== null ? this.apiGetDepartments(v) : (this.departments = []);
    },
    options() {
      this.options.catalogSubject = this.$route.params.id;
      this.apiGetItems();
    },
  },
  mounted() {
    this.apiGetFaculties();
  },
  methods: {
    async apiGetItems() {
      const options = GlobalMixin.methods.GlobalHandlingRequestParameters(
        ALLOWED_REQUEST_PARAMETERS.SPECIALTY_SUBJECTS,
        this.options,
      );
      try {
        const response = await api.get(API.SPECIALTY_SUBJECTS, options, { showLoader: true });
        const { data } = response;

        this.catalog = data.catalog;
        this.items = data.data;
        this.meta = data.meta;

        this.apiGetVerifications();
      } catch (e) {
        console.error(e); // TODO: show error
      }
    },
    apiGetVerifications() {
      api.get(API.VERIFICATION_CATALOG_SPECIALITY_STATUSES).then(({ data }) => {
        this.verificationsList = data.data;
      });
    },
    apiSetVerification(status) {
      api
        .patch(API.CATALOG_SPECIALTIES + '/verification', this.catalog.id, status)
        .then(() => {
          this.apiGetItems();
        })
        .catch((errors) => {
          console.error(errors.response.data);
        });
    },
    apiSetToggleToVerification(data) {
      api
        .patch(API.CATALOG_SPECIALTIES + '/toggle-to-verification', this.catalog.id, data)
        .then(() => {
          this.apiGetItems();
        })
        .catch((errors) => {
          console.error(errors.response.data);
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

      api.show(API.DEPARTMENTS, id).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },
    clear() {
      this.faculty = this.options.faculty = null;
      this.department = this.options.department = null;

      this.apiGetItems();
    },
    search() {
      this.options.faculty = this.faculty;
      this.options.department = this.department;

      this.apiGetItems();
    },
    store(data) {
      api
        .post(API.SPECIALTY_SUBJECTS, data)
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
    edit(data) {
      api
        .patch(API.SPECIALTY_SUBJECTS, data.id, data)
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
          this.item = null;
        })
        .catch((errors) => {
          this.$refs.editModal.setErrors(errors.response.data.errors);
        });
    },
    deleted(id, name) {
      this.$swal
        .fire({
          title: `Ви хочете видалити предмет?`,
          text: `${name}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api.destroy(API.SPECIALTY_SUBJECTS, id).then((response) => {
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
    openDialogPdf() {
      this.pdfModal = true;
    },
    closeDialogPdf() {
      this.pdfModal = false;
    },
    openDialogSetting() {
      this.settingModal = true;
    },
    closeDialogSetting() {
      this.settingModal = false;
    },
    openDialogEdit(item) {
      this.item = item;
      this.editModal = true;
    },
    closeDialogEdit() {
      this.editModal = false;
    },
    openDialogCreate() {
      this.createModal = true;
    },
    closeDialogCreate() {
      this.createModal = false;
    },
  },
};
</script>

<style scoped></style>
