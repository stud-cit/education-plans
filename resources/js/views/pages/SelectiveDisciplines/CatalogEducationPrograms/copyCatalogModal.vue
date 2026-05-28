<template>
  <v-dialog v-model="dialog" v-if="item" hide-overlay persistent width="800" transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-toolbar-title>Копіювання каталога</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text class="pb-0">
        <p class="title-catalog">
          Ви хочете скопіювати каталог за освітньою програмою
          <span class="">{{ item.education_program }} {{ item.year }} - {{ item.year + 1 }}р.</span>?
        </p>
      </v-card-text>
      <validation-observer ref="observer" v-slot="{ invalid }">
        <form @submit.prevent="submit" @keyup.enter="submit">
          <v-card-text class="pt-0">
            <v-container class="pt-0">
              <validation-provider v-slot="{ errors }" name="year" rules="required">
                <v-autocomplete v-model="year" :items="years" :error-messages="errors" label="Рік"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Освітня програма" rules="required">
                <v-autocomplete v-model="education_program" :items="object.education_programs" :error-messages="errors"
                  item-text="title" item-value="id" label="Освітня програма"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Рівень освіти" rules="required">
                <v-autocomplete v-model="item.education_level_id" :items="object.education_levels"
                  :error-messages="errors" item-text="title" item-value="id" persistent-hint
                  hint="(перший/другий/третій)" disabled label="Рівень освіти"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Кафедра, що пропонує дисципліну" rules="required">
                <v-autocomplete v-model="item.department_id" :items="departments" :error-messages="errors"
                  item-text="name" item-value="id" return-object class="mt-3" disabled
                  label="Кафедра, що пропонує дисципліну"></v-autocomplete>
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
  name: 'copyCatalogModal',
  data() {
    return {
      year: null,
      years: this.GlobalFakerYears(),
      education_program: null,
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
    item: {
      type: Object,
      default() {
        return {
          id: null,
          education_level_id: null,
          department_id: null,
        }
      },
    }
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
            catalog_id: this.item.id,
            year: this.year,
            catalog_education_level_id: this.item.education_level_id,
            faculty_id: this.item.faculty_id,
            department_id: this.item.department_id,
            education_program_id: this.education_program,
            speciality_id: this.object.education_programs.find(p => p.id == this.education_program)?.speciality_id,
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
      this.education_program = null;
      this.$refs.observer.reset();
    },
  },
};
</script>

<style scoped>
.title-catalog {
  padding-top: 40px;
  font-size: 16px;
}

.title-catalog span {
  font-weight: bold;
}
</style>
