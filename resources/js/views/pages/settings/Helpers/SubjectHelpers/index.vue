<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :server-items-length="meta.total"
      :options.sync="options"
      :footer-props="{ 'items-per-page-options': [15, 25, 50] }"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-row>
          <v-col cols="12" md="9">
            <v-text-field
              v-model="searchTitle"
              append-icon="mdi-magnify"
              label="Пошук за назвою"
              single-line
              hide-details
              class="mx-4"
            ></v-text-field>
          </v-col>
          <v-col align-self="center">
            <v-btn color="primary" outlined class="ml-2" @click="search"> Пошук </v-btn>
            <v-btn color="primary" class="ml-2" outlined @click="clear"> Очистити </v-btn>
          </v-col>
        </v-row>

        <v-row class="px-4 pb-4">
          <v-col cols="12">
            <v-autocomplete
              v-model="type"
              :items="types"
              item-text="title"
              item-value="id"
              label="Тип"
              hide-details
              clearable
            ></v-autocomplete>
          </v-col>
        </v-row>
      </template>

      <template v-slot:item.index="{ index }">
        {{ (meta.current_page - 1) * meta.per_page + (index + 1) }}
      </template>
      <template v-slot:item.actions="{ item }">
        <btn-tooltip tooltip="Редагувати">
          <v-icon small class="mr-2" color="primary" @click="openEdit(item)">
            mdi-pencil
          </v-icon>
        </btn-tooltip>
        <btn-tooltip tooltip="Видалити">
          <v-icon small class="mr-2" color="red" @click="deleted(item.id, item)">
            mdi-trash-can-outline
          </v-icon>
        </btn-tooltip>
      </template>
    </v-data-table>

    <v-tooltip left color="info">
      <template v-slot:activator="{ on, attrs }">
        <v-fab-transition>
          <v-btn color="primary" dark fixed bottom right fab v-bind="attrs" v-on="on" @click="dialog = true">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </v-fab-transition>
      </template>
      <span>Додати підсказку</span>
    </v-tooltip>

    <CreateOrUpdateModalSubjectHelpers
      :types="types"
      :dialog="dialog"
      :item="editItem"
      @submit="create"
      @update="update"
      @close="closeDialog"
      ref="createEditModal"
    />

  </v-container>
</template>

<script>
import api from "@/api";
import {ALLOWED_REQUEST_PARAMETERS, API} from '@/api/constants-api';
import GlobalMixin from "@/mixins/GlobalMixin";
import CreateOrUpdateModalSubjectHelpers from '@/views/pages/settings/Helpers/SubjectHelpers/modal'

export default {
  name: "CatalogHelpers",
  data() {
    return {
      dialog: false,
      searchTitle: '',
      types: [],
      type: '',
      headers: [
        { text: '№', value: 'index', sortable: false, width: '20px' },
        { text: 'Назва', value: 'title', sortable: false},
        { text: 'Тип', value: 'type', sortable: false},
        { text: 'Дії', value: 'actions', width: '80px', sortable: false},
      ],
      meta: [],
      items: [],
      options: null,
      editItem: null,
    }
  },
  components: {
    CreateOrUpdateModalSubjectHelpers
  },
  watch: {
    options() {
      this.apiGetItems();
    }
  },
  mounted() {
    this.apiGetCatalogHelperTypes();
  },
  methods: {
    apiGetItems() {
      const options = GlobalMixin.methods.GlobalHandlingRequestParameters(ALLOWED_REQUEST_PARAMETERS.GET_SUBJECT_HELPERS, this.options);
      api.get(API.SUBJECT_HELPERS, options ,{ showLoader: true }).then((response) => {
        const { data } = response;
        this.items = data.data;
        this.meta = data.meta;
      })
    },
    apiGetCatalogHelperTypes() {
      api.get(API.CATALOG_HELPER_TYPES).then((response) => {
        const { data } = response;
        this.types = data.data;
      })
    },
    create(item) {
      api.post(API.SUBJECT_HELPERS, item).then((response) => {
        this.dialog = false;
        const { message } = response.data;
        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });

        this.apiGetItems();
      }).catch((errors) => {
        this.$refs.createEditModal.setErrors(errors.response.data.errors);
      });
    },
    update(item) {
      api.put(API.SUBJECT_HELPERS + '/' + item.id, item).then((response) => {
        this.dialog = false;
        const { message } = response.data;
        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });
        this.apiGetItems();
      }).catch((errors) => {
        this.$refs.createEditModal.setErrors(errors.response.data.errors);
      });
    },
    deleted(id, item) {
      const text = '<h4> Тип - ' + item.type + '</h4>' + '<br><span class="help-title">'+ item.title +'</span>'
      this.$swal
        .fire({
          title: `Ви хочете видалити підсказку ?`,
          html: `${text}`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            api.destroy(API.SUBJECT_HELPERS, id).then((response) => {
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
    clear() {
      this.options.searchTitle = '';
      this.options.type = '';
      this.apiGetItems();
    },
    search() {
      this.options.searchTitle = this.searchTitle;
      this.options.type = this.type;
      this.apiGetItems();
    },
    openEdit(item) {
      this.editItem = {
        ...item,
        type: this.types.find(el => { if(el.title === item.type) return el.id })
      };
      this.dialog = true
    },
    closeDialog() {
      this.editItem = null;
      this.dialog = false
    }
  }
}
</script>

<style scoped>

</style>
