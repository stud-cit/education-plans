<template>
  <thead>
    <tr class="table-subtitle">
      <td class="border-table" colspan="100%">V. ПЛАН НАВЧАЛЬНОГО ПРОЦЕСУ</td>
    </tr>
    <tr>
      <td class="border-table" rowspan="8" width="20">№</td>
      <td class="border-table" rowspan="8" width="180">Назви навчальних дисциплін</td>
      <td class="border-table" rowspan="1" colspan="3" width="200">Розподіл контрольних заходів за семестрами</td>
      <td class="border-table" rowspan="8" width="50">Кількість кредитів ЄКТС</td>
      <td class="border-table" rowspan="1" colspan="6">Кількість годин</td>
      <td class="border-table no-print" rowspan="1" :colspan="plan.study_term.semesters">
        Розподіл кредитів за курсами, семестрами і модульними циклами
      </td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters * 2">
        Розподіл годин на тиждень за курсами, семестрами і модульними циклами
      </td>
      <td class="border-table d-print-none" rowspan="8">Кафедра викладання</td>
      <td class="border-table d-print-none" rowspan="8">Потоки</td>
    </tr>
    <tr>
      <td class="border-table" rowspan="7">Екзамени</td>
      <td class="border-table" rowspan="7">Заліки</td>
      <td class="border-table" rowspan="7">Індивідуальні завдання</td>

      <td class="border-table" rowspan="7">загальний обсяг</td>
      <td class="border-table" rowspan="1" colspan="4">аудиторних</td>
      <td class="border-table" rowspan="7">самостійна робота</td>
      <td class="border-table no-print" v-for="course in plan.study_term.course"
        :colspan="plan.study_term.semesters % plan.study_term.course > 0 && course === plan.study_term.course ? 1 : 2"
        :key="'course_noprint_' + course">
        {{ course }} курс
      </td>
      <td class="border-table" v-for="course in plan.study_term.course"
        :colspan="plan.study_term.semesters % plan.study_term.course > 0 && course === plan.study_term.course ? 2 : 4"
        :key="'course_' + course">
        {{ course }} курс
      </td>
    </tr>
    <tr>
      <td class="border-table" rowspan="6">всього</td>
      <td class="border-table" rowspan="1" colspan="3">у тому числі:</td>
      <td class="border-table no-print" rowspan="5" :colspan="plan.study_term.semesters">Семестри</td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters * 2">Семестри</td>
    </tr>
    <tr>
      <td class="border-table" colspan="3" rowspan="1"></td>
      <td class="border-table" v-for="semester in plan.study_term.semesters" colspan="2" :key="'semester_' + semester">
        {{ semester }}
      </td>
    </tr>
    <tr>
      <td class="border-table" rowspan="4">лекції</td>
      <td class="border-table" rowspan="4">практичні, семінарські</td>
      <td class="border-table" rowspan="4">лабораторні</td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters * 2">Модульні цикли</td>
    </tr>
    <tr>
      <template v-for="course in plan.study_term.course">
        <td class="border-table" rowspan="1" :key="'mod_1_' + course">I</td>
        <td class="border-table" rowspan="1" :key="'mod_2_' + course">II</td>
        <template v-if="plan.study_term.semesters % plan.study_term.course === 0 ||
          (plan.study_term.semesters % plan.study_term.course > 0 && course !== plan.study_term.course)
        ">
          <td class="border-table" rowspan="1" :key="'mod_3_' + course">III</td>
          <td class="border-table" rowspan="1" :key="'mod_4_' + course">IV</td>
        </template>
      </template>
    </tr>
    <tr>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters * 2">
        Кількість тижнів теоретичної підготовки в модульному циклі
      </td>
    </tr>
    <tr>
      <td class="border-table no-print" v-for="semester in plan.study_term.semesters" colspan="1"
        :key="'semester_' + semester">
        {{ semester }}
      </td>
      <template v-for="semester in plan.study_term.semesters">
        <td v-for="index in 2" :key="semester + '_' + index" rowspan="1" colspan="1" class="border-table">
          <template v-if="getMaxHour(semester, index, plan.hours_weeks_semesters)">
            {{ getMaxHour(semester, index, plan.hours_weeks_semesters).week }}
          </template>
        </td>
      </template>
    </tr>
    <tr>
      <td class="border-table" v-for="item in 12" :key="'num_1_' + item">
        {{ item }}
      </td>
      <td class="border-table no-print" v-for="item in plan.study_term.semesters" :key="item"></td>
      <td class="border-table" v-for="item in plan.study_term.semesters * 2" :key="'num_2_' + item">
        {{ item + 12 }}
      </td>
      <td class="border-table d-print-none" v-for="item in 2" :key="'num_3_' + item">
        {{ item + 12 + plan.study_term.semesters * 2 }}
      </td>
    </tr>
  </thead>
</template>

<script>
export default {
  name: 'ModularCyclicHeaderTable',
  props: {
    plan: {
      required: true,
    },
  },
  methods: {
    getMaxHour(semester, index, obj) {
      return obj.find((item) => item.semester === semester && item.index === index);
    },
  },
};
</script>

<!--<style >-->
<!--table tr > *:nth-child(38) {-->
<!--  display: none;-->
<!--}-->
<!--</style>-->
