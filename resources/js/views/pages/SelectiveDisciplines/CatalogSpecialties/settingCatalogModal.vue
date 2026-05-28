<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay persistent transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-toolbar-title
          >Налаштування: <span v-if="catalog">{{ catalog.title }}</span></v-toolbar-title
        >
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-tabs fixed-tabs background-color="primary" dark v-model="tab">
        <v-tab> Обмеження доступу </v-tab>
        <v-tab> Підписи </v-tab>
      </v-tabs>

      <v-tabs-items v-model="tab">
        <v-tab-item>
          <v-card max-width="1024" class="mx-auto py-10" elevation="0">
            <validation-observer ref="create" v-slot="{ invalid }">
              <form @submit.prevent="saveOwners" @keyup.enter="saveOwners">
                <validation-provider v-slot="{ errors }" name="Кафедрам, яким буде наданий доступ">
                  <v-autocomplete
                    v-model="department"
                    :items="departments"
                    :error-messages="errors"
                    item-text="name"
                    item-value="id"
                    return-object
                    class="mt-3"
                    chips
                    deletable-chips
                    multiple
                    :loading="departmentsLoading"
                    label="Кафедрам, яким буде наданий доступ"
                  ></v-autocomplete>
                </validation-provider>
                <v-btn color="primary" @click="saveOwners" :disabled="invalid"> Зберегти </v-btn>
              </form>
            </validation-observer>
          </v-card>
        </v-tab-item>
        <v-tab-item>
          <v-card max-width="1024" class="mx-auto py-10" elevation="0">
            <validation-observer ref="signatures" v-slot="{ invalid }">
              <form @submit.prevent="saveSignatures" @keyup.enter="saveSignatures">
                <v-row v-for="(signature, index) in signatures" :key="index">
                  <v-col v-if="signature.catalog_signature_type_id !== CATALOG_SIGNATURE_TYPE.manager.id">
                    <validation-provider
                      v-slot="{ errors }"
                      rules="required"
                      :name="
                        Object.values(CATALOG_SIGNATURE_TYPE).find(
                          (el) => el.id === signature.catalog_signature_type_id,
                        ).label
                      "
                    >
                      <v-autocomplete
                        v-model="signature.asu_id"
                        :items="workers"
                        :error-messages="errors"
                        item-text="full_name"
                        item-value="asu_id"
                        class="mt-3"
                        :label="
                          Object.values(CATALOG_SIGNATURE_TYPE).find(
                            (el) => el.id === signature.catalog_signature_type_id,
                          ).label
                        "
                      ></v-autocomplete>
                    </validation-provider>
                  </v-col>

                  <template v-else>
                    <v-col cols="12" lg="5" class="py-0">
                      <validation-provider v-slot="{ errors }" name="Кафедра" rules="required">
                        <v-autocomplete
                          v-model="signature.department_id"
                          :items="departments"
                          :error-messages="errors"
                          item-text="name"
                          item-value="id"
                          class="mt-3"
                          return-object
                          :loading="departmentsLoading"
                          label="Кафедра"
                          @change="setFacultySignature(signature, index)"
                        ></v-autocomplete>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12" lg="6" class="py-0">
                      <validation-provider
                        v-slot="{ errors }"
                        :name="
                          Object.values(CATALOG_SIGNATURE_TYPE).find(
                            (el) => el.id === signature.catalog_signature_type_id,
                          ).label
                        "
                        rules="required"
                      >
                        <v-autocomplete
                          v-model="signature.asu_id"
                          :items="workers"
                          :error-messages="errors"
                          item-text="full_name"
                          item-value="asu_id"
                          class="mt-3"
                          :label="
                            Object.values(CATALOG_SIGNATURE_TYPE).find(
                              (el) => el.id === signature.catalog_signature_type_id,
                            ).label
                          "
                        ></v-autocomplete>
                      </validation-provider>
                    </v-col>
                    <v-col cols="12" lg="1" class="d-flex align-center justify-center py-0">
                      <btn-tooltip tooltip="Видалити">
                        <v-btn outlined fab small color="red" @click="removeManager(index)">
                          <v-icon aria-hidden="false"> mdi-trash-can-outline </v-icon>
                        </v-btn>
                      </btn-tooltip>
                    </v-col>
                  </template>
                </v-row>

                <div class="text-center my-4">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn icon large v-bind="attrs" v-on="on" @click="addManager">
                        <v-icon>mdi-plus</v-icon>
                      </v-btn>
                    </template>
                    <span>Додати завідувача кафедри</span>
                  </v-tooltip>
                </div>

                <v-btn color="primary" @click="saveSignatures" :disabled="invalid"> Зберегти </v-btn>
              </form>
            </validation-observer>
          </v-card>
        </v-tab-item>
      </v-tabs-items>
    </v-card>
  </v-dialog>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { CATALOG_SIGNATURE_TYPE } from '@/utils/constants';

export default {
  name: 'settingCatalogModal',
  data() {
    return {
      department: null,
      departments: [],
      departmentsLoading: true,
      workers: [],
      signatures: [],
      tab: null,
      CATALOG_SIGNATURE_TYPE,
    };
  },
  watch: {
    dialog(v) {
      if (v === true) {
        this.apiGetDepartments();
        this.apiGetWorkers();
      }
    },
    catalog(v) {
      if (v !== null && 'owners' in v) {
        this.department = v.owners;
        this.signatures = v.signatures.length > 0 ? v.signatures : this.generateSignatures();
      }
    },
  },
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      },
    },
    catalog: null,
  },
  methods: {
    apiGetDepartments() {
      api.get(API.DEPARTMENTS).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },

    apiGetWorkers() {
      api.get(API.LIST_WORKERS).then((response) => {
        const { data } = response;
        this.workers = data;
      });
    },

    setFacultySignature(item, index) {
      this.signatures.map((signature, i) => {
        if (index === i) {
          signature.faculty_id = item.department_id.faculty_id;
          signature.department_id = item.department_id.id;
        }
      });
    },

    generateSignatures() {
      return [
        {
          id: null,
          department_id: this.catalog.department_id,
          faculty_id: this.catalog.faculty_id,
          catalog_subject_id: this.catalog ? this.catalog.id : this.$route.params.id,
          catalog_signature_type_id: CATALOG_SIGNATURE_TYPE.head.id,
          asu_id: null,
        },
        {
          id: null,
          department_id: this.catalog.department_id,
          faculty_id: this.catalog.faculty_id,
          catalog_subject_id: this.catalog ? this.catalog.id : this.$route.params.id,
          catalog_signature_type_id: CATALOG_SIGNATURE_TYPE.leader.id,
          asu_id: null,
        },
        {
          id: null,
          department_id: null,
          faculty_id: null,
          catalog_subject_id: this.catalog ? this.catalog.id : this.$route.params.id,
          catalog_signature_type_id: CATALOG_SIGNATURE_TYPE.manager.id,
          asu_id: null,
        },
      ];
    },

    close() {
      this.$emit('close');
    },

    saveOwners() {
      const data = {
        id: this.catalog.id,
        owners: this.department,
      };

      api.patch(API.SAVE_SPECIALITY_OWNERS, this.catalog.id, data).then((response) => {
        const { message } = response.data;
        this.$emit('init');

        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });
      });
    },

    saveSignatures() {
      api.patch(API.SAVE_SPECIALITY_SIGNATURE, this.catalog.id, { signatures: this.signatures }).then((response) => {
        const { message } = response.data;
        this.$emit('init');

        this.$swal.fire({
          position: 'center',
          icon: 'success',
          title: message,
          showConfirmButton: false,
          timer: 1500,
        });
      });
    },

    removeManager(index) {
      this.signatures.splice(this.signatures.indexOf(index), 1);
    },

    addManager() {
      this.signatures.push({
        id: null,
        department_id: null,
        faculty_id: null,
        catalog_subject_id: 8,
        catalog_signature_type_id: CATALOG_SIGNATURE_TYPE.manager.id,
        asu_id: null,
      });
    },
  },
};
</script>

<style scoped>
.btn-center {
  left: 50%;
  transform: translateX(-50%);
}
</style>
