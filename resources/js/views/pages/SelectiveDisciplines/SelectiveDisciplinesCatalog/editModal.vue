<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay persistent transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-toolbar-title>Редагування дисципліни</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <validation-observer ref="observer" v-slot="{ invalid }" v-if="item">
        <form @submit.prevent="submit" @keyup.enter="submit">
          <v-card-text>
            <v-container>
              <validation-provider v-slot="{ errors }" name="Оберіть каталог" rules="required">
                <v-autocomplete v-model="catalog" :items="catalogs" :error-messages="errors" item-text="title"
                  item-value="id" label="Оберіть каталог"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Назва дисципліни" rules="required">
                <v-autocomplete v-model="discipline" :items="disciplines" :error-messages="errors" item-text="title"
                  item-value="id" return-object disabled label="Назва дисципліни"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Інша назва дисципліни">
                <v-text-field label="Інша назва дисципліни" v-model="anotherDiscipline" :error-messages="errors"
                  :disabled="!discipline"></v-text-field>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Мова викладання" rules="required">
                <v-autocomplete v-model="language" multiple :items="languages" :error-messages="errors"
                  item-text="title" item-value="language_id" return-object label="Мова викладання"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }" name="Рівень освіти, для якого пропонується дисципліна"
                rules="required">
                <v-autocomplete v-model="educationLevel" :items="educationsLevel" :error-messages="errors"
                  item-text="title" item-value="id" persistent-hint hint="(перший/другий/третій)"
                  label="Рівень освіти, для якого пропонується дисципліна"></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }"
                name="Перелік галузей знань / спеціальностей, для яких пропонується дисципліна" rules="required">
                <v-autocomplete v-model="knowledgeSpecialty" :items="knowledgeSpecialties" :error-messages="errors"
                  item-text="title" item-value="id" return-object class="mt-3"
                  label="Перелік галузей знань / спеціальностей, для яких пропонується дисципліна"></v-autocomplete>
              </validation-provider>
              <validation-provider v-if="knowledgeSpecialty && knowledgeSpecialty.id === 2" v-slot="{ errors }"
                name="Перелік галузей знань / спеціальностей, для яких пропонується дисципліна"
                :rules="knowledgeSpecialty.id === 2 ? 'required' : ''">
                <v-radio-group v-model="selectListKnowledgeSpecialties" row :error-messages="errors">
                  <v-radio v-for="radio in radioBtnListKnowledgeSpecialties" :disabled="radio.disabled" :key="radio.id"
                    :label="radio.label" :value="radio"></v-radio>
                </v-radio-group>
              </validation-provider>

              <validation-provider v-if="knowledgeSpecialty && knowledgeSpecialty.id === 2" v-slot="{ errors }"
                :name="radioBtnListKnowledgeSpecialties.find((el) => el.id === selectListKnowledgeSpecialties.id).label"
                :rules="knowledgeSpecialty.id === 2 ? 'required' : ''">
                <v-autocomplete v-model="listKnowledgeSpecialties" :items="listsKnowledgeSpecialties"
                  :error-messages="errors" :item-text="radioBtnListKnowledgeSpecialties.find((el) => el.id === selectListKnowledgeSpecialties.id).itemText
                    " hide-details item-value="id" return-object multiple class="mt-3" :label="radioBtnListKnowledgeSpecialties.find((el) => el.id === selectListKnowledgeSpecialties.id).label
                      "></v-autocomplete>
              </validation-provider>

              <validation-provider v-if="knowledgeSpecialty && knowledgeSpecialty.id === 3" v-slot="{ errors }"
                name="Спеціальності" :rules="knowledgeSpecialty.id === 3 ? 'required' : ''">
                <v-autocomplete v-model="listKnowledgeSpecialties" :items="listsKnowledgeSpecialties"
                  :error-messages="errors" item-text="title" hide-details item-value="id" return-object multiple
                  class="mt-3" label="Спеціальності"></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Кафедра, що пропонує дисципліну" rules="required">
                <v-autocomplete v-model="department" :items="departments" :error-messages="errors" item-text="name"
                  item-value="id" return-object class="mt-3" label="Кафедра, що пропонує дисципліну"></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Лекції">
                <v-autocomplete v-model="lecture" :items="teachers" :error-messages="errors" item-text="full_name"
                  item-value="asu_id" return-object class="mt-3" label="Лекції" multiple></v-autocomplete>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Семінарські та практичні заняття, лабораторні роботи"
                rules="required">
                <v-autocomplete v-model="practice" :items="teachers" :error-messages="errors" item-text="full_name"
                  item-value="asu_id" return-object class="mt-3"
                  label="Семінарські та практичні заняття, лабораторні роботи" multiple></v-autocomplete>
              </validation-provider>
              <validation-provider v-slot="{ errors }"
                name="Загальна компетентність, на формування або розвиток якої спрямована дисципліна" rules="required">
                <v-combobox v-model="generalCompetence" :items="helpersGeneralCompetence" item-value="id"
                  item-text="title" :error-messages="errors"
                  label="Загальна компетентність, на формування або розвиток якої спрямована дисципліна"></v-combobox>
              </validation-provider>

              <validation-provider v-slot="{ errors }" name="Результати навчання за навчальною дисципліною"
                rules="required">
                <v-combobox v-model="learningOutcomes" :items="helpersLearningOutcomes" item-value="id"
                  item-text="title" :error-messages="errors"
                  label="Результати навчання за навчальною дисципліною"></v-combobox>
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
  name: 'EditSelectiveDisciplinesCatalogModal',
  data() {
    return {
      catalog: null,
      catalogs: [],

      disciplines: [],
      discipline: null,
      anotherDiscipline: null,
      languages: [],
      language: null,
      educationsLevel: [],
      educationLevel: null,

      knowledgeSpecialties: [
        { id: 1, title: 'Для всіх ОП' },
        { id: 2, title: 'Для всіх здобувачів освіти крім:' },
        { id: 3, title: 'Для спеціальностей:' },
      ],
      knowledgeSpecialty: null,
      radioBtnListKnowledgeSpecialties: [
        { id: 1, label: 'Інститут/факультет', type: 'faculty', itemText: 'name', disabled: true },
        { id: 2, label: 'Спеціальностей', type: 'specialty', itemText: 'title', disabled: false },
        { id: 3, label: 'Освітні програми', type: 'education_program', itemText: 'title', disabled: false },
      ],
      selectListKnowledgeSpecialties: null,
      listsKnowledgeSpecialties: [],
      listKnowledgeSpecialties: null,

      departments: [],
      department: null,

      teachers: [],
      lecture: null,
      practice: null,

      helpersGeneralCompetence: [],
      generalCompetence: null,

      helpersLearningOutcomes: [],
      learningOutcomes: null,

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

      divisions: [
        { id: 1, apiPath: API.FACULTIES, label: 'faculties' },
        { id: 2, apiPath: API.SPECIALITIES_ALL, label: 'specialities' },
        { id: 3, apiPath: API.EDUCATIONAL_PROGRAMS_ALL, label: 'educational_programs' },
      ],
      subject: null,
      url: null,
      url_mix: null,
    };
  },
  created() {
    this.restrictionsSemester = this.radioRestrictionsSemester[0];
  },
  watch: {
    knowledgeSpecialty(v) {
      if (v !== null && v.id === 1) {
        this.selectListKnowledgeSpecialties = null;
      } else if (v !== null && v.id === 3) {
        this.selectListKnowledgeSpecialties = this.radioBtnListKnowledgeSpecialties[1];
      } else {
        this.selectListKnowledgeSpecialties = this.radioBtnListKnowledgeSpecialties[1];
      }
    },
    selectListKnowledgeSpecialties(v) {
      if (v !== null) {
        if (v.type !== this.subject.list_fields_knowledge.type) {
          this.listKnowledgeSpecialties = null;
        }
        this.apiGetKnowledgeSpecialtiesDepartments(v.id);
      }
    },
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
    item(v) {
      if (v !== null && this.dialog === true) {
        this.apiGetItem(v.id);
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
    item: null,
  },
  methods: {
    apiGetItem(id) {
      api.edit(API.CATALOG_SELECTIVE_SUBJECTS, id, { showLoader: true }).then(({ data }) => {
        const {
          discipline,
          catalog_subject_id,
          language,
          education_level,
          department_id,
          lecturers,
          practice,
          general_competence,
          learning_outcomes,
          types_educational_activities,
          entry_requirements_applicants,
          number_acquirers,
          list_fields_knowledge,
          limitation,
          url,
          url_mix,
        } = data.data;

        this.subject = data.data;

        this.catalog = catalog_subject_id;
        this.discipline = discipline.id;
        this.language = language;
        this.anotherDiscipline = discipline.title_en;
        this.educationLevel = education_level;
        this.department = department_id;
        this.lecture = lecturers;
        this.practice = practice;
        this.generalCompetence = general_competence;
        this.learningOutcomes = learning_outcomes;
        this.typesTrainingSessions = types_educational_activities;
        this.numberAcquirers = number_acquirers;
        this.requirements = entry_requirements_applicants;
        this.knowledgeSpecialty = this.knowledgeSpecialties.find((el) => el.title === list_fields_knowledge.label);
        this.selectListKnowledgeSpecialties = this.radioBtnListKnowledgeSpecialties.find(
          (el) => el.type === list_fields_knowledge.type,
        );
        this.listKnowledgeSpecialties = list_fields_knowledge.list;
        this.restrictionsSemester = this.radioRestrictionsSemester.find((el) => el.label === limitation.label);
        this.semesters = limitation.semesters;
        this.url = url;
        this.url_mix = url_mix;
      });
    },
    apiGetCreate() {
      api.get(API.CATALOG_SELECTIVE_SUBJECTS + '/create', null, { showLoader: true }).then(({ data }) => {
        const {
          catalogs,
          subjects,
          languages,
          educationsLevel,
          departments,
          teachers,
          helpersGeneralCompetence,
          helpersResultsOfStudy,
          helpersTypesTrainingSessions,
          helpersRequirements,
          faculties,
        } = data;
        this.catalogs = catalogs;
        this.disciplines = subjects;
        this.languages = languages;
        this.educationsLevel = educationsLevel;
        this.departments = departments;
        this.teachers = teachers;
        this.helpersGeneralCompetence = helpersGeneralCompetence;
        this.helpersLearningOutcomes = helpersResultsOfStudy;
        this.helpersTypesTrainingSessions = helpersTypesTrainingSessions;
        this.helpersRequirements = helpersRequirements;
        this.listsKnowledgeSpecialties = faculties;
      });
    },
    apiGetKnowledgeSpecialtiesDepartments(v) {
      api.get(this.divisions.find((el) => el.id === v).apiPath).then(({ data }) => {
        this.listsKnowledgeSpecialties = data.data;
      });
    },
    close() {
      this.$emit('close');
    },
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          const listFieldsKnowledge = {
            label: this.knowledgeSpecialty.title,
            type: this.selectListKnowledgeSpecialties ? this.selectListKnowledgeSpecialties.type : null,
            type_name: this.selectListKnowledgeSpecialties ? this.selectListKnowledgeSpecialties.label : null,
            list: this.listKnowledgeSpecialties,
          };

          // TODO: видалення type_name при умові, що knowledgeSpecialty буде Для спеціальностей
          if (this.knowledgeSpecialty.id === 3) {
            listFieldsKnowledge.type_name = null;
          }

          const limitation = {
            label: this.restrictionsSemester.label,
            semesters: this.semesters,
          };

          this.$emit('submit', {
            id: this.subject.id,
            catalog_subject_id: this.catalog,
            asu_id: this.subject.discipline.id,
            title: this.subject.discipline.title,
            title_en: this.anotherDiscipline,
            catalog_education_level_id: this.educationLevel,
            language: this.language,
            lecturers: this.lecture,
            practice: this.practice,
            list_fields_knowledge: JSON.stringify(listFieldsKnowledge),
            department_id: this.subject.department_id,
            faculty_id: this.subject.faculty_id,
            general_competence: this.generalCompetence,
            learning_outcomes: this.learningOutcomes,
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
      this.catalog = null;
      this.discipline = null;
      this.anotherDiscipline = null;
      this.educationLevel = null;
      this.language = null;
      this.lecture = null;
      this.practice = null;
      this.knowledgeSpecialty = null;
      this.selectListKnowledgeSpecialties = this.radioBtnListKnowledgeSpecialties[0];
      this.listKnowledgeSpecialties = null;
      this.department = null;
      this.generalCompetence = null;
      this.learningOutcomes = null;
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
