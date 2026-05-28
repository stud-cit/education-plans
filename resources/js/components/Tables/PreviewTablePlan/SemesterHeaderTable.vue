<template>
  <thead>
    <tr class="table-subtitle">
      <td class="border-table" colspan="100%">
        V. ПЛАН НАВЧАЛЬНОГО ПРОЦЕСУ
      </td>
    </tr>
    <tr>
      <td class="border-table" rowspan="6">№</td>
      <td class="border-table" rowspan="6">Назви навчальних дисциплін</td>
      <td class="border-table" rowspan="1" colspan="3">Розподіл контрольних заходів за семестрами</td>
      <td class="border-table" rowspan="6">Кількість кредитів ЄКТС</td>
      <td class="border-table" rowspan="1" colspan="6">Кількість годин</td>
      <td class="border-table no-print" rowspan="1" :colspan="plan.study_term.semesters">
        Розподіл кредитів за курсами, семестрами і модульними циклами
      </td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters">
        Розподіл годин на тиждень за курсами, семестрами і модульними циклами
      </td>
      <td class="border-table d-print-none" rowspan="6">Кафедра викладання</td>
      <td class="border-table d-print-none" rowspan="6">Потоки</td>
    </tr>
    <tr>
      <td class="border-table" rowspan="5">Екзамени</td>
      <td class="border-table" rowspan="5">Заліки</td>
      <td class="border-table" rowspan="5">Індивідуальні завдання</td>

      <td class="border-table" rowspan="5">загальний обсяг</td>
      <td class="border-table" rowspan="1" colspan="4">аудиторних</td>
      <td class="border-table" rowspan="5">самостійна робота</td>
      <td class="border-table no-print" v-for="course in plan.study_term.course" rowspan="1"
        :colspan="plan.study_term.semesters % plan.study_term.course > 0 && course === plan.study_term.course ? 1 : 2"
        :key="'course_noprint_' + course">
        {{ course }} курс
      </td>
      <td class="border-table" v-for="course in plan.study_term.course" rowspan="1"
        :colspan="plan.study_term.semesters % plan.study_term.course > 0 && course === plan.study_term.course ? 1 : 2"
        :key="'course_' + course">
        {{ course }} курс
      </td>
    </tr>
    <tr>
      <td class="border-table" rowspan="4">всього</td>
      <td class="border-table" rowspan="1" colspan="3">у тому числі:</td>
      <td class="border-table no-print" rowspan="3" :colspan="plan.study_term.semesters">Семестри</td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters">Семестри</td>
    </tr>
    <tr>
      <td class="border-table" colspan="3" rowspan="1"></td>
      <td class="border-table" v-for="semester in plan.study_term.semesters" colspan="1" :key="'semester_' + semester">
        {{ semester }}
      </td>
    </tr>
    <tr>
      <td class="border-table" rowspan="2">лекції</td>
      <td class="border-table" rowspan="2">практичні, семінарські</td>
      <td class="border-table" rowspan="2">лабораторні</td>
      <td class="border-table" rowspan="1" :colspan="plan.study_term.semesters">
        Кількість тижнів теоретичної підготовки в модульному циклі
      </td>
    </tr>
    <tr>
      <td class="border-table no-print" v-for="semester in plan.study_term.semesters" colspan="1"
        :key="'semester_' + semester">
        {{ semester }}
      </td>
      <template v-for="semester in plan.study_term.semesters">
        <td class="border-table" rowspan="1" colspan="1">
          <template v-if="getMaxHour(semester, plan.hours_weeks_semesters)">
            {{ getMaxHour(semester, plan.hours_weeks_semesters).week }}
          </template>
        </td>
      </template>
    </tr>
    <tr>
      <td class="border-table" v-for="item in 12" :key="'num_1_' + item">
        {{ item }}
      </td>
      <td class="no-print" :colspan="plan.study_term.semesters"></td>
      <td class="border-table" v-for="item in plan.study_term.semesters" :key="'num_2_' + item">
        {{ item + 12 }}
      </td>
      <td class="border-table d-print-none" v-for="item in 2" :key="'num_3_' + item">
        {{ item + 12 + plan.study_term.semesters }}
      </td>
    </tr>
  </thead>
</template>

<script>
export default {
  name: "SemesterHeaderTable",
  props: {
    plan: {
      required: true,
    }
  },
  methods: {
    getMaxHour(semester, obj) {
      return obj.find((item) => item.semester === semester)
    },
  }
}
</script>

<style scoped></style>
