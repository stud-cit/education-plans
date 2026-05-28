<template>
  <validation-observer ref="observer" v-slot="{ invalid }">
    <form @submit.prevent="submit" @keyup.enter="submit">
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Назва плану" rules="required">
            <span class="body-2 yellow lighten-3"><b>Формат назви:</b> Код спеціальності Назва ОП Освітній рівень Рік
              прийому. Приклад: 122 ОПП Інформатика
              бакалавр 2023</span>
            <v-text-field v-model="plan.title" :error-messages="errors" :disabled="readOnly || isShortPlan"
              label="Назва плану" required></v-text-field>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Галузь знань " rules="">
            <v-autocomplete v-model.number="plan.field_knowledge_id" :items="fieldsKnowledge" :error-messages="errors"
              :disabled="readOnly || isShortPlan" item-text="title" item-value="id"
              label="Галузь знань "></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Спеціальність" rules="">
            <v-autocomplete v-model.number="plan.speciality_id" :items="specialities" :loading="specialitiesLoading"
              :disabled="specialities.length === 0 || readOnly || isShortPlan" :error-messages="errors"
              item-text="title" item-value="id" label="Спеціальність"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Освітня програма" rules="">
            <v-autocomplete v-model.number="plan.education_program_id" :items="educationalPrograms"
              :loading="educationalProgramsLoading"
              :disabled="educationalPrograms.length === 0 || readOnly || isShortPlan" :error-messages="errors"
              item-text="title" item-value="id" label="Освітня програма"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Спеціалізація" rules="">
            <v-autocomplete v-model.number="plan.specialization_id" :items="specializations"
              :loading="specializationsLoading" :error-messages="errors" item-text="title" item-value="id"
              label="Спеціалізація"
              :disabled="specializations.length === 0 || readOnly || isShortPlan"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Освітня кваліфікація" rules="">
            <v-autocomplete v-model="plan.qualification_id" :items="qualifications" :error-messages="errors"
              :disabled="readOnly || isShortPlan" item-text="title" item-value="id"
              label="Освітня кваліфікація"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="pb-0">
          <validation-provider v-slot="{ errors }" name="Професійна кваліфікація" rules="">
            <v-autocomplete v-model="plan.profession_qualification_id" :items="professionQualifications"
              :error-messages="errors" :disabled="readOnly || isShortPlan" item-text="title" item-value="id"
              label="Професійна кваліфікація"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Факультет" rules="">
            <v-autocomplete v-model="plan.faculty_id" :items="faculties" :error-messages="errors"
              :disabled="readOnly || isShortPlan" item-text="name" item-value="id" label="Факультет"></v-autocomplete>
          </validation-provider>
        </v-col>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Кафедра" rules="">
            <v-autocomplete v-model="plan.department_id" :items="departments" :error-messages="errors"
              :loading="departmentsLoading" :disabled="departmentsLoading || readOnly || isShortPlan" item-text="name"
              item-value="id" label="Кафедра"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Форма організації навчання" rules="required">
            <v-autocomplete v-model="plan.form_organization_id" :items="formsOrganizationStudy" :error-messages="errors"
              item-text="title" item-value="id" label="Форма організації навчання" required
              :disabled="edit || readOnly || isShortPlan"></v-autocomplete>
          </validation-provider>
        </v-col>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Форма навчання" rules="required">
            <v-autocomplete v-model="plan.form_study_id" :items="formsStudy" :error-messages="errors"
              :disabled="readOnly || isShortPlan" item-text="title" item-value="id"
              label="Форма навчання"></v-autocomplete>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Термін навчання" rules="required">
            <v-autocomplete v-model="plan.study_term" :items="termsStudy" :error-messages="errors" item-text="title"
              label="Термін навчання" return-object item-value="id" :disabled="edit || isShortPlan"></v-autocomplete>
          </validation-provider>
        </v-col>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Кількість семестрів" rules="required">
            <v-text-field v-model="numberSemesters" :error-messages="errors" label="Кількість семестрів" disabled
              required></v-text-field>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Освітній (Освітньо-науковий) рівень" rules="required">
            <v-autocomplete v-model="plan.education_level_id" :items="educationsLevel" :error-messages="errors"
              :disabled="readOnly || isShortPlan" item-text="title" item-value="id"
              label="Освітній (Освітньо-науковий) рівень"></v-autocomplete>
          </validation-provider>
        </v-col>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Обсяг плану в кредитах"
            rules="required|numeric|min:1|max:3|min_value:1">
            <v-text-field v-model="plan.credits" :error-messages="errors" :disabled="readOnly || isShortPlan"
              label="Обсяг плану в кредитах" required type="number" :min="1" :max="999"></v-text-field>
          </validation-provider>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="6" class="py-0">
          <validation-provider v-slot="{ errors }" name="Рік прийому" rules="required|numeric|digits:4">
            <v-autocomplete v-model="plan.year" :items="years" :error-messages="errors"
              :disabled="readOnly || isShortPlan" label="Рік прийому"></v-autocomplete>
          </validation-provider>
        </v-col>
        <v-col cols="12" lg="6" class="py-0" v-if="allowedRoles([
          ROLES.ID.admin,
          ROLES.ID.root,
          ROLES.ID.training_department,
          ROLES.ID.practice_department,
          ROLES.ID.educational_department_deputy,
          ROLES.ID.educational_department_chief,
          ROLES.ID.admin_department_postgraduate,
        ])
        ">
          <v-checkbox :disabled="readOnly || isShortPlan" v-model="plan.published"
            label="Відкрити спільний доступ"></v-checkbox>
        </v-col>
      </v-row>
      <v-row v-if="objHoursWeeks.length">
        <v-col>
          <table>
            <thead>
              <tr>
                <td v-for="course in studyTerm.course" :key="course"
                  :colspan="(objHoursWeeks.length / studyTerm.semesters) * 2">
                  {{ course }} курс
                </td>
              </tr>
              <tr>
                <td v-for="semester in studyTerm.semesters" :key="semester"
                  :colspan="objHoursWeeks.length / studyTerm.semesters">
                  {{ semester }} семестр
                </td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td :colspan="objHoursWeeks.length">Кількість тижнів теоретичної підготовки в модульному циклі</td>
              </tr>
              <tr>
                <td v-for="(item, index) in objHoursWeeks" :key="index">
                  <validation-provider v-slot="{ errors }" rules="required|numeric|min_value:0"
                    name="Кількість тижнів теоретичної підготовки в модульному циклі"
                    :vid="'weeds_' + item.index + '_' + item.course">
                    <v-text-field v-model.number="item.week" :error-messages="errors"
                      :readonly="readOnly || isShortPlan" required type="number" :min="0" dense
                      hide-details></v-text-field>
                  </validation-provider>
                </td>
              </tr>

              <tr>
                <td :colspan="objHoursWeeks.length">
                  Максимальна кількість годин в тижні модульного циклу
                </td>
              </tr>
              <tr>
                <td v-for="(item, index) in objHoursWeeks" :key="index">
                  <validation-provider v-slot="{ errors }" rules="required|numeric|min_value:0"
                    name="Максимальна кількість годин в тижні модульного циклу"
                    :vid="'hours_' + item.index + '_' + item.course">
                    <v-text-field v-model.number="item.hour" :error-messages="errors" required
                      :readonly="readOnly || isShortPlan" type="number" :min="0" dense hide-details></v-text-field>
                  </validation-provider>
                </td>
              </tr>
            </tbody>
          </table>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-btn v-if="!readOnly" class="mr-4" type="submit" color="primary" :loading="submitLoading"
            :disabled="invalid">
            Зберегти
          </v-btn>
        </v-col>
      </v-row>
    </form>
  </validation-observer>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { FORM_ORGANIZATIONS_TABLE, ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';
import { mapState, mapGetters } from 'vuex';

export default {
  name: 'General',
  data() {
    return {
      ROLES,
      faculties: [],
      departments: [],
      departmentsLoading: false,
      educationsLevel: [],
      formsStudy: [],
      studyTerm: null,
      termsStudy: [],
      years: this.GlobalFakerYears(),
      numberSemesters: null,
      specialities: [],
      specialitiesLoading: false,
      specializations: [],
      specializationsLoading: false,
      educationalPrograms: [],
      educationalProgramsLoading: false,
      qualifications: [],
      professionQualifications: [],
      fieldsKnowledge: [],
      formsOrganizationStudy: [],
      objHoursWeeks: [],
    };
  },
  props: {
    plan: {
      type: Object,
    },
    edit: {
      type: Boolean,
    },
  },
  computed: {
    ...mapState('plans', ['submitLoading']),
    ...mapGetters({ isShortPlan: 'plans/isShortPlan', 'readOnly': 'plans/readOnly' }),
  },
  mounted() {
    this.apiGetFields();
  },
  mixins: [RolesMixin],
  watch: {
    plan(v) {
      if (v !== null && v.study_term !== null) {
        this.buildObjHoursWeeks();
      }
      if (v !== null && v.field_knowledge_id !== null) {
        this.apiGetSpecialities(v.field_knowledge_id);
      }
      if (v !== null && v.speciality_id !== null) {
        this.apiGetEducationPrograms(v.speciality_id);
        this.apiGetSpecializations(v.speciality_id);
      }
    },
    'plan.faculty_id'(v) {
      if (v !== null) this.apiGetDepartments(v);
    },
    'plan.study_term'(v) {
      if (v !== null) {
        this.numberSemesters = v.semesters;
        this.studyTerm = v;
        this.buildObjHoursWeeks();
      }
    },
    'plan.form_organization_id'() {
      this.buildObjHoursWeeks();
    },
    'plan.field_knowledge_id'(v, old) {
      if (old !== null && v !== old) {
        this.$store.dispatch('plans/setValue', { speciality_id: null });
      }
      v !== null ? this.apiGetSpecialities(v) : (this.specialities = []);
    },
    'plan.speciality_id'(v, old) {
      if (old !== null && v !== old) {
        this.$store.dispatch('plans/setValue', { specialization_id: null });
        this.$store.dispatch('plans/setValue', { education_program_id: null });
      }
      v !== null ? this.apiGetSpecializations(v) : (this.specializations = []);
      v !== null ? this.apiGetEducationPrograms(v) : (this.educationalPrograms = []);
    },
  },
  methods: {
    apiGetFields() {
      api.get(API.PLAN_CREATE).then(({ data }) => {
        this.faculties = data.faculties ?? [];
        this.educationsLevel = data.educations_level ?? [];
        this.formsStudy = data.forms_study ?? [];
        this.termsStudy = data.terms_study ?? [];
        this.qualifications = data.qualifications ?? [];
        this.professionQualifications = data.professionQualifications ?? [];
        this.fieldsKnowledge = data.fields_knowledge ?? [];
        this.formsOrganizationStudy = data.forms_organizationStudy ?? [];
      });
    },
    apiGetDepartments(id) {
      this.departmentsLoading = true;

      api.show(API.DEPARTMENTS, id).then(({ data }) => {
        this.departments = data.data;
        this.departmentsLoading = false;
      });
    },
    apiGetSpecializations(id) {
      this.specializationsLoading = true;

      api.show(API.SPECIALIZATIONS, id).then(({ data }) => {
        this.specializations = data.data;
        this.specializationsLoading = false;
      });
    },
    apiGetSpecialities() {
      this.specialitiesLoading = true;

      api.get(API.SPECIALITIES).then(({ data }) => {
        this.specialities = data.data;
        this.specialitiesLoading = false;
      });
    },
    apiGetEducationPrograms(id) {
      this.educationalProgramsLoading = true;

      api.show(API.EDUCATIONAL_PROGRAMS, id).then(({ data }) => {
        this.educationalPrograms = data.data;
        this.educationalProgramsLoading = false;
      });
    },
    buildObjHoursWeeks() {
      const result = [];
      const studyTerm = this.studyTerm;
      const formOrganization = this.plan.form_organization_id;

      if (studyTerm !== null && formOrganization !== null) {
        if (
          this.plan !== null &&
          studyTerm.id === this.plan.study_term_id &&
          formOrganization === this.plan.form_organization_id &&
          this.plan.hours_weeks_semesters
        ) {
          this.objHoursWeeks = this.plan.hours_weeks_semesters;
          return;
        }

        let currentSemester = 1;
        for (let course = 1; course <= studyTerm.course; course++) {
          const obj = { course };
          for (let semester = 1; semester <= 2; semester++) {
            if (currentSemester <= studyTerm.semesters) {
              obj.semester = currentSemester;

              for (let item = 1; item <= FORM_ORGANIZATIONS_TABLE[formOrganization]; item++) {
                result.push({
                  ...obj,
                  week: '',
                  hour: 0,
                  index: item,
                });
              }
            }
            currentSemester++;
          }
        }
        this.objHoursWeeks = result;
      } else {
        this.objHoursWeeks = [];
      }
    },
    submit() {
      this.$refs.observer
        .validate()
        .then((validated) => {
          if (validated) {
            const data = {
              ...this.plan,
              study_term_id: this.plan.study_term.id,
              number_semesters: this.numberSemesters,
              hours_weeks_semesters: JSON.stringify(this.objHoursWeeks),
            };

            delete data.schedule_education_process;
            delete data.summary_data_budget_time;
            delete data.practical_training;

            this.$store.dispatch('plans/setSubmitLoading', true);
            this.$emit('submit', data);
          }
        })
        .catch(() => {
          this.$store.dispatch('plans/setSubmitLoading', false);
        });
    },
  },
};
</script>

<style lang="css" scoped>
table {
  width: 100%;
  border: 1px solid #dee2e6;
  border-collapse: collapse;
  width: 100%;
}

table td {
  text-align: center;
  color: #000;
  font-size: 14px;
  padding: 0.75rem;
  border: 1px solid #dee2e6;
}
</style>
