<template>
  <div>
    <v-row :class="[
      'cycle-subject',
      hasErrors || subjectIndexError == subject.id || !subject.verification ? 'error' : '',
      'ma-0',
      'mb-1',
      getSubjectError(subject.id) == subject.id ? 'error' : ''
    ]">
      <v-col cols="5" class="pa-0 text-left">
        <v-icon v-if="subject.subjects.length > 0">mdi-chevron-down</v-icon>
        <v-icon v-if="subject.subject_id">mdi-circle-small</v-icon>
        {{ subject.checkCountCreditSubjects }}
        {{ subject.selective_discipline_id ? subject.selective_discipline.title : subject.title }}
      </v-col>
      <v-col class="pa-0">
        {{ subject.hours }}
      </v-col>
      <v-col class="pa-0">
        {{ subject.practices }}
      </v-col>
      <v-col class="pa-0">
        {{ subject.laboratories }}
      </v-col>
      <v-col class="pa-0">
        {{ sumHour(subject.hours_modules, 'hour').toFixed(1) }}
      </v-col>
      <v-col class="pa-0">
        {{ subject.credits }}
      </v-col>
      <v-col class="pa-0">
        {{subject.exams ? subject.exams.map((item) => item.semester)[0] : ''}}
      </v-col>
      <v-col class="pa-0 text-right">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="readOnly || isShortPlan &&
              (item.list_cycle_id === CYCLES.PRACTICAL_TRAINING || item.list_cycle_id === CYCLES.ATTESTATION)
              " small icon @click="editSubject(subject, item)" v-bind="attrs" v-on="on">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </template>
          <span>Редагувати</span>
        </v-tooltip>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="readOnly || isShortPlan" small icon @click="delSubject(subject)" v-bind="attrs" v-on="on">
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </template>
          <span>Видалити</span>
        </v-tooltip>
        <v-tooltip bottom v-if="!subject.subject_id">
          <template v-slot:activator="{ on, attrs }">
            <v-btn :disabled="readOnly || isShortPlan" small icon @click="addSubSubject(subject, item)" v-bind="attrs"
              v-on="on">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </template>
          <span>Додати піддисципліну</span>
        </v-tooltip>
      </v-col>
    </v-row>
    <SubjectItem v-for="(child, subjectIndex) in subject.subjects" :key="'subject' + subjectIndex" :parentItem="subject"
      v-bind:subject="child" :item="item" />
  </div>
</template>
<script>
import { eventBus } from '@/main';
import { mapGetters } from 'vuex';
import { CYCLES } from '@/utils/constants';
import SubjectItem from '@/views/pages/plan/tabs/SubjectItem.vue';

export default {
  name: 'SubjectItem',
  props: {
    subject: {
      type: Object,
      required: true,
    },
    item: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      CYCLES,
      subjectIndexError: null,
    };
  },
  computed: {
    ...mapGetters({
      isShortPlan: 'plans/isShortPlan',
      readOnly: 'plans/readOnly',
      subjectCalculatorError: 'plans/subjectCalculatorError',
    }),
    hasErrors() {
      if (this.subject.subjects.length > 0) {
        return false;
      }

      return this.subjectIndexError == this.subject.id ||
        (this.item.has_discipline && !this.subject.checkCountHoursModules) ||
        (this.item.has_discipline && this.subject.checkCountHours) ||
        (this.item.has_discipline && this.subject.checkLastHourModule != null) ||
        (this.item.has_discipline && !this.subject.checkHasCreditsSemester) ||
        (this.item.has_discipline && this.subject.checkCountHoursSemester.length > 0)
    }
  },
  mounted() {
    this.checkCredit();
  },
  methods: {
    getSubjectError(id) {
      return this.subjectCalculatorError(id);
    },
    sumHour(array, field) {
      return array.map((item) => item[field]).reduce((prev, curr) => +prev + +curr, 0);
    },
    checkCredit() {
      let sumCredits = this.item.subjects
        .map((subjectItem) => subjectItem.credits)
        .reduce((prev, curr) => prev + curr, 0);
      if (sumCredits > this.item.credit) {
        this.subjectIndexError = this.subject.id;
        this.$store.dispatch(
          'plans/setErrorsPlan',
          `Перевищено суму кредитів дисциплін в циклі ${this.item.title}: ${sumCredits} із ${this.item.credit}`,
        );
      } else {
        this.subjectIndexError = null;
        this.$store.dispatch('plans/setErrorsPlan', null);
      }
    },
    editSubject(subject, cycle) {
      eventBus.$emit('editSubject', { subject, cycle });
    },
    delSubject(subject) {
      eventBus.$emit('delSubject', subject);
    },
    addSubSubject(subject, cycle) {
      eventBus.$emit('addSubSubject', { subject, cycle });
    },
  },
};
</script>
