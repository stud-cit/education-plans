<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay persistent transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-toolbar-title>Створити дисципліну</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <validation-observer ref="observer" v-slot="{ invalid }">
        <form @submit.prevent="submit" @keyup.enter="submit">
          <v-card-text>
            <v-container>
              <validation-provider v-slot="{ errors }" name="Назва дисципліни" rules="required">
                <v-autocomplete v-model="discipline" :items="disciplines" :error-messages="errors" item-text="title"
                  item-value="id" return-object label="Назва дисципліни"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Інша назва дисципліни">
                <v-text-field label="Інша назва дисципліни" v-model="anotherDiscipline" :error-messages="errors"
                  :disabled="!discipline"></v-text-field>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Мова викладання" rules="required">
                <v-autocomplete v-model="language" multiple :items="languages" :error-messages="errors"
                  item-text="title" item-value="language_id" return-object label="Мова викладання"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Кафедра, що пропонує дисципліну" rules="required">
                <v-autocomplete v-model="department" :items="departments" :error-messages="errors" item-text="name"
                  item-value="id" return-object class="mt-3" label="Кафедра, що пропонує дисципліну"></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Лекції">
                <v-autocomplete v-model="lecture" :items="teachers" :error-messages="errors" item-text="full_name"
                  item-value="asu_id" return-object class="mt-3" label="Лекції" chips deletable-chips
                  multiple></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Семінарські та практичні заняття, лабораторні роботи"
                rules="required">
                <v-autocomplete v-model="practice" :items="teachers" :error-messages="errors" item-text="full_name"
                  item-value="asu_id" return-object class="mt-3"
                  label="Семінарські та практичні заняття, лабораторні роботи" chips deletable-chips
                  multiple></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }"
                name="Компетентності (загальні та/або фахові, на розвиток яких спрямована дисципліна)" rules="required">
                <v-combobox v-model="generalCompetence" :items="helpersGeneralCompetence" item-value="id"
                  item-text="title" :error-messages="errors"
                  label="Компетентності (загальні та/або фахові, на розвиток яких спрямована дисципліна)"></v-combobox>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Результати навчання за навчальною дисципліною"
                rules="required">
                <v-combobox v-model="resultsOfStudy" :items="helpersResultsOfStudy" item-value="id" item-text="title"
                  :error-messages="errors" label="Результати навчання за навчальною дисципліною"></v-combobox>
              </validation-provider>

              <validation-provider v-slot="{ errors }"
                name="Види навчальних занять та методи викладання, що пропонуються" rules="required">
                <v-combobox v-model="typesTrainingSessions" :items="helpersTypesTrainingSessions" item-value="id"
                  item-text="title" :error-messages="errors"
                  label="Види навчальних занять та методи викладання, що пропонуються"></v-combobox>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Кількість здобувачів, які можуть записатися на дисципліну"
                rules="required|numeric">
                <v-text-field v-model="numberAcquirers" :error-messages="errors" type="number"
                  label="Кількість здобувачів, які можуть записатися на дисципліну"></v-text-field>
              </validation-provider>

              <validation-provider v-slot="{ errors }"
                name="Вхідні вимоги до здобувачів, які хочуть обрати дисципліну/вимоги до матеріально-технічного забезпечення"
                rules="required">
                <v-combobox v-model="requirements" :items="helpersRequirements" item-value="id" item-text="title"
                  :error-messages="errors"
                  label="Вхідні вимоги до здобувачів, які хочуть обрати дисципліну/вимоги до матеріально-технічного забезпечення"></v-combobox>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Обмеження щодо семестру вивчення" rules="required">
                <v-radio-group v-model="restrictionsSemester" row :error-messages="errors">
                  <template v-slot:label>
                    <div class="label">Обмеження щодо семестру вивчення:</div>
                  </template>
                  <v-radio v-for="radio in radioRestrictionsSemester" :key="radio.id" :label="radio.label"
                    :value="radio"></v-radio>
                </v-radio-group>
              </validation-provider>

              <validation-provider v-if="restrictionsSemester.id === 2" v-slot="{ errors }" name="Виберіть семестр/и"
                :rules="restrictionsSemester.id ? 'required' : ''">
                <v-select v-model="semesters" :items="[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :error-messages="errors"
                  disable-lookup chips deletable-chips hide-selected label="Виберіть семестр/и" multiple
                  @change="(v) => v.sort((a, b) => a - b)"></v-select>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Посилання на силабус" rules="max:2048">
                <v-text-field v-model="url" :error-messages="errors" type="url"
                  label="Посилання на силабус"></v-text-field>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Посилання на НМК дисципліни на платформі Mix"
                rules="required|max:2048">
                <v-text-field v-model="url_mix" :error-messages="errors" type="url"
                  label="Посилання на НМК дисципліни на платформі Mix"></v-text-field>
              </validation-provider>

            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="secondary" @click="close"> Закрити </v-btn>
            <v-btn color="primary" @click="submit" :disabled="invalid"> Зберегти </v-btn>
          </v-card-actions>
        </form>
      </validation-observer>
    </v-card>
  </v-dialog>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';

export default {
  name: 'CreateSpecialitySubjectModal',
  data() {
    return {
      disciplines: [],
      discipline: null,
      anotherDiscipline: null,
      languages: [],
      language: null,

      departments: [],
      department: null,

      teachers: [],
      lecture: null,
      practice: null,

      helpersGeneralCompetence: [],
      generalCompetence: null,

      helpersResultsOfStudy: [],
      resultsOfStudy: null,

      helpersTypesTrainingSessions: [],
      typesTrainingSessions: null,

      numberAcquirers: null,

      helpersRequirements: [],
      requirements: null,

      radioRestrictionsSemester: [
        { id: 1, label: 'Відповідно до навчального плану' },
        { id: 2, label: 'Крім:' },
      ],
      restrictionsSemester: null,
      semesters: null,
      url: null,
      url_mix: null,
    };
  },
  created() {
    this.restrictionsSemester = this.radioRestrictionsSemester[0];
  },
  watch: {
    dialog(v) {
      if (v === true) {
        this.apiGetCreate();
      }
    },
    discipline(v) {
      if (v && v.title_en) {
        this.anotherDiscipline = v.title_en;
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
    catalog: {
      type: Object,
      default() {
        return {
          id: this.$route.params.id,
          title: '',
        };
      },
    },
  },
  methods: {
    apiGetCreate() {
      api.get(API.EDUCATION_PROGRAM_SUBJECTS + '/create', null, { showLoader: true }).then(({ data }) => {
        const {
          subjects,
          languages,
          departments,
          teachers,
          helpersGeneralCompetence,
          helpersResultsOfStudy,
          helpersTypesTrainingSessions,
          helpersRequirements,
        } = data;
        this.disciplines = subjects;
        this.languages = languages;
        this.departments = departments;
        this.teachers = teachers;
        this.helpersGeneralCompetence = helpersGeneralCompetence;
        this.helpersResultsOfStudy = helpersResultsOfStudy;
        this.helpersTypesTrainingSessions = helpersTypesTrainingSessions;
        this.helpersRequirements = helpersRequirements;
      });
    },
    close() {
      this.$emit('close');
    },
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          const limitation = {
            label: this.restrictionsSemester.label,
            semesters: this.semesters,
          };

          this.$emit('submit', {
            catalog_subject_id: this.catalog.id,
            asu_id: this.discipline.id,
            title: this.discipline.title,
            title_en: this.anotherDiscipline,
            language: this.language,
            lecturers: this.lecture,
            practice: this.practice,
            department_id: this.department.id,
            faculty_id: this.department.faculty_id,
            general_competence: this.generalCompetence,
            learning_outcomes: this.resultsOfStudy,
            types_educational_activities: this.typesTrainingSessions,
            number_acquirers: this.numberAcquirers,
            entry_requirements_applicants: this.requirements,
            limitation: JSON.stringify(limitation),
            url: this.url,
            url_mix: this.url_mix,
          });
        }
      });
    },
    setErrors(errors) {
      this.$refs.observer.setErrors(errors);
    },
    clear() {
      this.discipline = null;
      this.anotherDiscipline = null;
      this.language = null;
      this.lecture = null;
      this.practice = null;
      this.department = null;
      this.generalCompetence = null;
      this.resultsOfStudy = null;
      this.typesTrainingSessions = null;
      this.numberAcquirers = null;
      this.requirements = null;
      this.restrictionsSemester = this.radioRestrictionsSemester[0];
      this.semesters = null;
      this.url = null;
      this.url_mix = null;
      this.$refs.observer.reset();
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
