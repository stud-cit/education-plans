<template>
  <v-dialog v-model="dialog" hide-overlay persistent width="800" transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-toolbar-title>Створити каталог</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>


      <validation-observer ref="observer" v-slot="{ invalid }">
        <form @submit.prevent="submit" @keyup.enter="submit">
          <v-card-text>
            <v-container>
              <validation-provider v-slot="{ errors }" name="Рік" rules="required">
                <v-autocomplete v-model="year" :items="years" :error-messages="errors" label="Рік"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Спеціальність" rules="required">
                <v-autocomplete v-model="speciality" :items="object.specialties" :error-messages="errors"
                  item-text="title" item-value="id" label="Спеціальність"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Рівень освіти" rules="required">
                <v-autocomplete v-model="educationLevel" :items="object.education_levels" :error-messages="errors"
                  item-text="title" item-value="id" persistent-hint hint="(перший/другий/третій)"
                  label="Рівень освіти"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Кафедра, що пропонує дисципліну" rules="required">
                <v-autocomplete v-model="department" :items="departments" :error-messages="errors" item-text="name"
                  item-value="id" return-object class="mt-3" label="Кафедра, що пропонує дисципліну"></v-autocomplete>
              </validation-provider>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="secondary" @click="close">
              Закрити
            </v-btn>
            <v-btn color="primary" @click="submit" :disabled="invalid">
              Зберегти
            </v-btn>
          </v-card-actions>
        </form>
      </validation-observer>
    </v-card>
  </v-dialog>
</template>

<script>

import { API } from "@/api/constants-api";
import api from "@/api";

export default {
  name: 'createCatalogModal',
  data() {
    return {
      year: null,
      years: this.GlobalFakerYears(),
      speciality: null,
      educationLevel: null,
      department: null,
      departments: [],
    };
  },
  props: {
    object: {
      type: Object,
      default() {
        return {
          specialties: [],
          education_levels: [],
        }
      },
    },
    dialog: {
      type: Boolean,
      default() {
        return false;
      },
    },
  },
  mounted() {
    this.apiGetDepartments();
  },
  methods: {
    apiGetDepartments() {
      api.get(API.DEPARTMENTS, null, { showLoader: true }).then(({ data }) => {
        this.departments = data.data;
      })
    },
    close() {
      this.$emit('close');
      this.clear();
    },
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          this.$emit('submit', {
            year: this.year,
            catalog_education_level_id: this.educationLevel,
            faculty_id: this.department.faculty_id,
            department_id: this.department.id,
            speciality_id: this.speciality
          });
        }
      });
    },
    setErrors(errors) {
      this.$refs.observer.setErrors(errors);
    },
    clear() {
      this.year = null;
      this.educationLevel = null;
      this.department = null;
      this.speciality = null;
      this.$refs.observer.reset();
    },
  },
};
</script>

<style scoped></style>
