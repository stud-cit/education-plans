<template>
  <div>
    <v-row no-gutters v-if="!readOnly">
      <v-col cols="12" sm="8" md="10" lg="10">
        <v-autocomplete :loading="schedulesLoading" v-model="schedule" return-object :items="schedules"
          item-text="title" item-value="id" class="mt-3 pb-0" label="Графіки" dense>
        </v-autocomplete>
      </v-col>

      <v-col cols="12" sm="4" md="2" lg="2" class="d-flex">
        <v-spacer></v-spacer>
        <v-btn color="primary" class="mb-3" elevation="2" :disabled="schedule === null"
          @click="fillTable()">Застосувати</v-btn>
      </v-col>
    </v-row>
    <ValidationObserver ref="observer" v-slot="{ valid, invalid, errors }">
      <table>
        <tr>
          <th :colspan="year.weeks + 1">І . ГРАФІК НАВЧАЛЬНОГО ПРОЦЕСУ, тижні</th>
        </tr>
        <tr>
          <td rowspan="2">Курс</td>
          <td v-for="(month, index) in year.header" :key="index" :colspan="month.countWeek">
            {{ month.monthTitle }}
          </td>
        </tr>
        <tr>
          <td v-for="(week, i) in year.weeks" :key="i">
            {{ i + 1 }}
          </td>
        </tr>
        <tr v-for="(k, cursIndex) in year.courses" :key="cursIndex">
          <td>
            {{ cursIndex + 1 }}
          </td>
          <td v-for="(week, i) in k" :key="i">
            <ValidationProvider :vid="'data_' + i + '_row_' + week.course + '_col_' + week.month"
              :rules="'oneOf:' + rule" name="Тиждень" v-slot="{ errors }">
              <input :disabled="readOnly || isShortPlan" type="text" :class="[errors[0] ? 'errors' : '']"
                v-model="week.val" @input="(val) => (week.val = week.val.toUpperCase())" />
            </ValidationProvider>
          </td>
        </tr>
        <tfoot>
          <td :colspan="year.weeks + 1" class="text-left pa-2">
            <p class="text-bold mb-0" v-if="noteLoaded">ПОЗНАЧЕННЯ: {{ notes }}</p>
            <v-skeleton-loader v-else v-bind="notes" type="sentences,text@1"></v-skeleton-loader>
          </td>
        </tfoot>
      </table>

      <v-alert outlined type="error" :value="hasErrors(errors)">
        Ви ввели недопустиме значення, зверніть увагу на розкладку клавіатури допускається тільки українська!
      </v-alert>

      <SummaryDataBudgetTime :items="data.summary_data_budget_time" :course="data.study_term.course"
        ref="summary_data_budget_time" />
      <br />
      <v-row>
        <v-col>
          <PracticalTraining ref="practical_training" :items="plan.practical_training" />
        </v-col>
        <v-col>
          <table>
            <tr>
              <th colspan="3">ІV. АТЕСТАЦІЯ</th>
            </tr>
            <tr>
              <td>№</td>
              <td>Форма</td>
              <td>Семестр</td>
            </tr>
            <template v-if="plan.exams_table.length">
              <tr v-for="(item, index) in plan.exams_table" :key="'subject_' + index">
                <td>{{ ++index }}</td>
                <td>{{ item.title }}</td>
                <td>{{ item.semester }}</td>
              </tr>
            </template>
          </table>
        </v-col>
      </v-row>
      <v-btn v-if="!readOnly" class="mt-4" color="primary" :disabled="invalid" type="submit" @click="save()"> Зберегти
      </v-btn>
    </ValidationObserver>
  </div>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { mapGetters } from 'vuex';
import SummaryDataBudgetTime from '@c/Tables/TitleTablePlan/SummaryDataBudgetTime';
import PracticalTraining from '@c/Tables/TitleTablePlan/PracticalTraining';

export default {
  name: 'Title',
  components: { PracticalTraining, SummaryDataBudgetTime },
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      schedule: null,
      schedules: [],
      schedulesLoading: false,
      rule: 'Т,Т*,С,П,К,А,Д,т,т*,с,п,к,а,д',
      notes: '',
      noteLoaded: false,
      year: {
        weeks: 0,
        header: [],
        courses: [],
      },
      month: [
        'Січень',
        'Лютий',
        'Березень',
        'Квітень',
        'Травень',
        'Червень',
        'Липень',
        'Серпень',
        'Вересень',
        'Жовтень',
        'Листопад',
        'Грудень',
      ],
    };
  },
  computed: {
    ...mapGetters({
      plan: 'plans/plan',
      isShortPlan: 'plans/isShortPlan',
      readOnly: 'plans/readOnly'
    }),
  },
  mounted() {
    this.getSchedules();
    this.getRules();
    this.getScheduleEducationProcessData();
  },
  methods: {
    fillTable() {
      this.$swal.fire({
        title: 'Навчальний графік буде перезаписано!',
        text: `${this.schedule.title}`,
        showDenyButton: true,
        confirmButtonText: 'Так',
        denyButtonText: `Ні`,
        focusDeny: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.getSchedule(this.schedule.id);
        }
      })

    },
    save() {
      this.$refs.observer.validate().then((response) => {
        if (response) {
          const data = {
            ...this.data,
            hours_weeks_semesters: JSON.stringify(this.data.hours_weeks_semesters),
            schedule_education_process: JSON.stringify(this.year),
            summary_data_budget_time: JSON.stringify(this.$refs.summary_data_budget_time.result),
            practical_training: JSON.stringify(this.$refs.practical_training.result),
          };
          this.$emit('submit', data);
        }
      });
    },

    start() {
      for (let index = 0; index < this.data.study_term.course; index++) {
        this.year.courses.push([]);
      }

      for (let index = 0; index < 12; index++) {
        var date = new Date(this.data.year, 9 + index, 0);
        var countWeek = this.getWeeks(date.getFullYear(), date.getMonth(), 0);
        this.year.weeks += countWeek;

        this.year.header.push({
          monthTitle: this.month[date.getMonth()],
          countWeek,
        });

        for (let course = 0; course < this.year.courses.length; course++) {
          for (let countWeekIndex = 0; countWeekIndex < countWeek; countWeekIndex++) {
            this.year.courses[course].push({
              month: index,
              course,
              val: '',
            });
          }
        }
      }
    },

    getWeeks(year, month) {
      var l = new Date(year, month + 1, 0);
      var result = Math.floor((l.getDate() - (l.getDay() ? l.getDay() : 7)) / 7 + 1);
      return result;
    },
    hasErrors(obj) {
      let result = false;

      for (const prop in obj) {
        if (obj[prop].length > 0) {
          result = true;
          break;
        }
      }
      return result;
    },
    getScheduleEducationProcessData() {
      if (this.data.schedule_education_process) {
        this.year = this.data.schedule_education_process;
      } else {
        this.start();
      }
    },
    getRules() {
      api.get(`${API.NOTES}/rules/${this.plan.year}`).then((response) => {
        this.noteLoaded = true;
        const { rule, notes } = response.data.data;
        this.rule = rule + ',Т*'; // TODO: MAY BY LOOP OVER JSON GET UNIQUE LETTERS
        this.notes = notes;
      });
    },
    getSchedules() {
      this.schedulesLoading = true;

      this.$store.dispatch('plans/getListSchedule', {
        education_level_id: this.plan.education_level_id, study_term_id: this.plan.study_term_id
      }).then((data) => {
        this.schedules = data.data;
        this.schedulesLoading = false;
      });
    },
    getSchedule(id) {
      const isComplete = this.$store.dispatch('plans/getSchedule', id);
      if (isComplete) {
        this.$swal.fire({
          title: "Успішно оновлено",
          text: "ГРАФІК НАВЧАЛЬНОГО ПРОЦЕСУ",
          icon: "success"
        });
      }
    }
  },
};
</script>

<style lang="css" scoped>
table {
  width: 100%;
  font-size: 12px;
  border: 1px solid #dee2e6;
  border-collapse: collapse;
  margin-bottom: 15px;
}

table td {
  text-align: center;
  color: #000;
  font-size: 14px;
  border: 1px solid #dee2e6;
  width: 19.5px;
}

table th {
  padding: 10px 0;
}

table td input {
  width: 100%;
  height: 100%;
  text-align: center;
  outline: none;
}

table td input:focus {
  border: 1px solid #000;
  box-sizing: border-box;
}

table tfoot {
  font-weight: bold;
  border: 0;
}

.errors {
  border: solid 2px red;
}
</style>
