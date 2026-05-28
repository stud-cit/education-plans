<template>
  <v-data-table :headers="headers" :items="items" :server-items-length="meta && meta.total ? meta.total : -1"
    :options.sync="options" :footer-props="{ 'items-per-page-options': [15, 25, 50] }" class="elevation-1 plans-table"
    :item-class="this.itemRowBackground" :loading="!items.length">

    <template v-slot:top>
      <v-row>
        <v-col cols="12" md="6">
          <v-text-field v-model="searchTitle" append-icon="mdi-magnify" label="Пошук за назвою" single-line hide-details
            class="mx-4"></v-text-field>
        </v-col>
        <v-col cols="12" md="2">
          <v-select v-model="type" :items="planTypes" item-text="title" item-value="id" select hide-details label="Тип"
            clearable class="mx-4"></v-select>
        </v-col>
        <v-col align-self="center">
          <v-btn color="primary" outlined class="ml-2" @click="search"> Пошук </v-btn>
          <v-btn color="primary" class="ml-2" outlined :input-value="filterToggle"
            @click="filterToggle = !filterToggle">
            <v-icon> mdi-filter </v-icon>
          </v-btn>
          <v-btn color="primary" class="ml-2" outlined @click="clear"> Очистити </v-btn>
        </v-col>
      </v-row>

      <v-row v-show="filterToggle" class="px-4 pb-4">
        <v-col cols="12" lg="6" v-if="exceptRoles([ROLES.ID.department])">
          <v-autocomplete v-model="faculty" :items="faculties" item-text="name" item-value="id" label="Факультет"
            hide-details clearable></v-autocomplete>
        </v-col>
        <v-col cols="12" lg="6" v-if="exceptRoles([ROLES.ID.department])">
          <v-autocomplete v-model="department" :items="departments" item-text="name" item-value="id" label="Кафедра"
            hide-details :loading="departmentsLoading" clearable></v-autocomplete>
        </v-col>
        <v-col cols="12" lg="6" v-if="exceptRoles([ROLES.ID.guest])">
          <v-autocomplete v-model="division" :items="divisions" item-text="title" item-value="id" hide-details
            label="Представник відділу" clearable></v-autocomplete>
        </v-col>
        <v-col cols="12" lg="6" v-if="exceptRoles([ROLES.ID.guest])">
          <v-select v-model="verificationDivisionStatus" :items="verificationsDivisionsStatus"
            :disabled="division === null" item-text="title" item-value="id" select hide-details
            label="Статус верифікації" clearable></v-select>
        </v-col>
        <v-col cols="12" lg="6" v-if="allowedRoles([ROLES.ID.root])">
          <v-text-field v-model="planId" label="Пошук за ID плану" single-line hide-details type="number"
            clearable></v-text-field>
        </v-col>
        <v-col cols="12" lg="6" v-if="exceptRoles([ROLES.ID.guest])">
          <v-checkbox v-model="archived" label="Включити арховані документи"></v-checkbox>
        </v-col>
        <v-col cols="12" md="6" v-if="exceptRoles([ROLES.ID.guest])">
          <v-text-field v-model="filter_year" label="Рік" single-line hide-details></v-text-field>
        </v-col>
      </v-row>
    </template>

    <template v-slot:item.index="{ index, item }">
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <span v-bind="attrs" v-on="on" class="cursor-pointer">
            {{ (meta.current_page - 1) * meta.per_page + (index + 1) }}
          </span>
        </template>
        <span>ID: {{ item.id }}</span>
      </v-tooltip>
    </template>

    <template v-slot:item.type_id="{ item }">
      <span class="text-no-wrap">
        <PublishedBadge :published="item.published" /><span class="text-wrap">{{ item.type_id }}</span>
      </span>
    </template>

    <template v-slot:item.title="{ item }">
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            {{ item.title }}
          </div>
        </template>
        <span>Автор: {{ item.author }}</span>
      </v-tooltip>
    </template>

    <template v-slot:item.created_at="{ item }">
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            {{ item.created_at.split(' ')[0] }}
          </div>
        </template>
        <span>{{ item.created_at }}</span>
      </v-tooltip>
    </template>

    <template v-slot:item.catalog_speciality="{ item }">
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            {{ item.catalog_speciality ? 'Так' : 'Ні' }}
          </div>
        </template>
        <span v-if="item.catalog_speciality">Присутній каталог дисциплін за СПЕЦІАЛЬНІСТЮ</span>
        <span v-else>Відсутній каталог дисциплін за СПЕЦІАЛЬНІСТЮ</span>
      </v-tooltip>
    </template>

    <template v-slot:item.catalog_education_programs="{ item }">
      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <div v-bind="attrs" v-on="on">
            {{ item.catalog_education_programs ? 'Так' : 'Ні' }}
          </div>
        </template>
        <span v-if="item.catalog_education_programs">Присутній каталог дисциплін за ОСВІТНЬОЮ ПРОГРАМОЮ</span>
        <span v-else>Відсутній каталог дисциплін за ОСВІТНЬОЮ ПРОГРАМОЮ</span>
      </v-tooltip>
    </template>

    <template v-slot:item.actions="{ item }">
      <btn-tooltip tooltip="Перегляд">
        <router-link v-if="item.actions.preview"
          :to="{ name: 'PreviewPlan', params: { id: item.id, title: item.title.replaceAll('/', '-') } }"
          target="_blank">
          <v-icon small class="mr-2" color="primary">mdi-eye-outline</v-icon>
        </router-link>
      </btn-tooltip>
      <btn-tooltip tooltip="Скопіювати">
        <v-icon v-if="item.actions.copy" small class="mr-1" color="primary" @click="$emit('copy', item.id, item.title)">
          mdi-content-copy
        </v-icon>
      </btn-tooltip>
      <btn-tooltip tooltip="Створити проєкт плану">
        <v-icon v-if="item.actions.project" small class="mr-1" color="primary"
          @click="$emit('project', item.id, item.title)">
          mdi-folder-cog-outline
        </v-icon>
      </btn-tooltip>
      <btn-tooltip tooltip="Редагувати">
        <v-icon v-if="item.actions.edit" small class="mr-1" color="primary" @click="$emit('edit', item.id, item.title)">
          mdi-pencil-outline
        </v-icon>
      </btn-tooltip>
      <btn-tooltip tooltip="Архівувати">
        <v-icon v-if="item.actions.delete" small color="red" @click="$emit('delete', item.id, item.title)">
          mdi-archive-arrow-down-outline
        </v-icon>
      </btn-tooltip>
      <btn-tooltip tooltip="Відновити">
        <v-icon v-if="item.actions.restore" small color="primary" @click="$emit('restore', item.id, item.title)">
          mdi-archive-arrow-up-outline
        </v-icon>
      </btn-tooltip>
    </template>
  </v-data-table>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import RolesMixin from '@/mixins/RolesMixin';
import BackgroundRowMixin from '@/mixins/BackgroundRowMixin';
import { ROLES } from '@/utils/constants';
import { mapGetters } from 'vuex';
import PublishedBadge from '@/components/base/PublishedBadge';

export default {
  name: 'PlansTable',
  data() {
    return {
      isRestoring: false,
      ROLES,
      filterToggle: false,
      searchTitle: '',
      headers: [],
      faculty: null,
      faculties: [],
      department: null,
      departments: [],
      departmentsLoading: false,
      division: null,
      divisions: [],
      verificationDivisionStatus: 1,
      verificationsDivisionsStatus: [],
      planId: null,
      type: null,
      planTypes: [],
      archived: false,
      filter_year: null,
    };
  },
  components: {
    PublishedBadge,
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    meta: {
      type: Object,
      default: () => { },
    },
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
    }),
    options: {
      get: function () {
        return this.$store.state.plans.options;
      },
      set: function (newValue) {

        if (this.isRestoring && newValue.page === 1 && this.$store.state.plans.options.page > 1) {
          this.$nextTick(() => {
            this.options = { ...this.options };
          });
          return;
        }

        this.$store.dispatch('loader/show');
        this.$store.dispatch('plans/setOptions', this.filterSort(newValue));
        this.updateUrlParameters(newValue);
        this.$emit('update', this.options);
      },
    },
  },
  mixins: [RolesMixin, BackgroundRowMixin],
  watch: {
    items() {
      this.isRestoring = false;

      if (this.meta && this.meta.current_page && this.meta.current_page !== this.options.page) {
        const correctOptions = {
          ...this.options,
          page: this.meta.current_page
        };
        this.$store.dispatch('plans/setOptions', correctOptions);
      }
    },
    faculty(v) {
      v !== null ? this.apiGetDepartments(v) : (this.departments = []);
    },
    division(v) {
      if (v === null) {
        this.options.divisionWithStatus = null;
      }
    },
  },
  mounted() {
    this.apiGetDivisions();
    this.generateHeader();
    this.restoreStateFromUrl();
  },
  methods: {

    restoreStateFromUrl() {
      const q = this.$route.query;

      let restoredSortBy = [];
      let restoredSortDesc = [];

      if (q.sort_by) {
        restoredSortBy = [q.sort_by];
        restoredSortDesc = [q.sort_desc === 'true'];
      }

      if (q.search) this.searchTitle = q.search;
      if (q.year) this.filter_year = q.year;
      if (q.planId) this.planId = q.planId;

      if (q.type) this.type = Number(q.type);

      if (q.faculty && this.exceptRoles([ROLES.ID.department])) {
        this.faculty = Number(q.faculty)
      } else {
        this.faculty = this.user.faculty_id;
      }

      const departmentPresentInFaculty = this.departments

      if (q.department && departmentPresentInFaculty) {
        this.department = Number(q.department);
      } else {
        this.department = null;
        delete q.department;
      }
      if (q.division) this.division = Number(q.division);
      if (q.verificationStatus) this.verificationDivisionStatus = Number(q.verificationStatus);

      if (q.archived) this.archived = q.archived === 'true';

      if (q.faculty || q.department || q.division || q.planId || q.archived || q.year) {
        this.filterToggle = true;
      }

      if (q.page || q.items_per_page || q.sort_by) {
        const restoredPage = q.page ? Number(q.page) : 1;
        const restoredItemsPerPage = q.items_per_page ? Number(q.items_per_page) : 15;

        if (restoredPage > 1) {
          this.isRestoring = true;
        }

        this.options = {
          ...this.options,
          page: restoredPage,
          itemsPerPage: restoredItemsPerPage,
          sortBy: restoredSortBy,
          sortDesc: restoredSortDesc,
        };
      } else {
        this.search();
      }
    },

    updateUrlParameters(newOptions = null) {
      const options = newOptions || this.options;
      const query = {
        search: this.searchTitle,
        type: this.type,
        faculty: this.faculty,
        department: this.department,
        division: this.division,
        verificationStatus: this.division ? this.verificationDivisionStatus : null,
        planId: this.planId,
        archived: this.archived ? 'true' : null,
        year: this.filter_year,

        page: options.page,
        items_per_page: options.itemsPerPage,

        sort_by: this.options.sortBy && this.options.sortBy.length ? this.options.sortBy[0] : null,
        sort_desc: this.options.sortDesc && this.options.sortDesc.length ? this.options.sortDesc[0] : null,
      }

      Object.keys(query).forEach(key => {
        if (query[key] === null || query[key] === undefined || query[key] === '') {
          delete query[key];
        }
      });

      const currentQuery = this.$route.query;
      const isSame = JSON.stringify(query) === JSON.stringify(currentQuery);

      if (!isSame) {
        this.$router.replace({ query }).catch(() => { });
      }
    },
    generateHeader() {
      let headers = [
        { text: '№', value: 'index', sortable: false },
        { text: 'Тип', value: 'type_id', sortable: true },
        { text: 'Назва', value: 'title' },
        { text: 'Факультет', value: 'faculty', sortable: false },
        { text: 'Кафедра', value: 'department', sortable: false },
        { text: 'Рік', value: 'year', width: '70px', align: 'center' },
        { text: 'Дата створення', value: 'created_at', width: '150px', align: 'center' },
        { text: 'Верифіковано', value: 'verification', sortable: false, align: 'center' },
        { text: 'СП', value: 'catalog_speciality', sortable: false, width: '30px' },
        { text: 'ОП', value: 'catalog_education_programs', sortable: false, width: '30px' },
        { text: 'Дії', value: 'actions', width: '120px', sortable: false },
      ];

      if (this.allowedRoles([ROLES.ID.guest])) {
        headers = headers.filter((item) =>
          item.value != 'verification' && item.value != 'created_at' &&
          item.value != 'catalog_speciality' && item.value != 'catalog_education_programs'
        );
      }

      this.headers = headers;
    },
    update() {
      this.$emit('update', this.options);
    },
    search() {
      this.$store.dispatch('loader/show');
      const newOptions = { ...this.options, page: 1 };
      this.$store.dispatch('plans/setOptions', this.filterSort(newOptions));
      this.updateUrlParameters();
      this.$emit('update', this.options);
    },
    clear() {
      this.$store.dispatch('loader/show');
      this.searchTitle = '';

      if (this.exceptRoles([ROLES.ID.department])) {
        this.department = null;
      }

      if (this.exceptRoles([ROLES.ID.faculty_institute, ROLES.ID.department])) {
        this.faculty = null;
      }

      this.division = null;
      this.planId = null;
      this.archived = false;
      this.type = null;
      this.filter_year = null;

      this.options = {
        ...this.options,
        page: 1,
        sortBy: [],
        sortDesc: [],
      };
    },
    apiGetDivisions() {
      api.get(API.PLAN_FILTERS).then(({ data }) => {
        this.divisions = data.divisions;
        this.verificationsDivisionsStatus = data.verificationsStatus;
        this.faculties = data.faculties;
        this.planTypes = data.types;
      });
    },
    apiGetDepartments(id) {
      this.departmentsLoading = true;

      api.show(API.DEPARTMENTS, id).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },
    filterSort(values) {
      const params = {
        ...values,
        page: values.page,
        items_per_page: values.itemsPerPage,
        sort_by: values.sortBy && values.sortBy.length ? values.sortBy[0] : null,
        sort_desc: values.sortDesc && values.sortDesc.length ? values.sortDesc[0] : false,
        searchTitle: this.searchTitle,
        planId: this.planId,
        archived: +this.archived,
        type: this.type,
        filter_year: this.filter_year ? +this.filter_year : null,
      };

      if (this.exceptRoles([ROLES.ID.department])) {
        params.faculty = this.faculty;
        params.department = this.department;
      }

      if (this.division !== null) {
        params.divisionWithStatus = [this.division, this.verificationDivisionStatus];
      } else {
        params.divisionWithStatus = null;
      }

      return params;
    },
  },
};
</script>

<style>
.v-data-table>.v-data-table__wrapper>table>tbody>tr>td {
  padding: 0 10px !important;
}
</style>
