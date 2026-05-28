<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay persistent transition="dialog-bottom-transition">
    <v-card v-if="item && subject !== null">
      <v-toolbar dark color="primary">
        <v-toolbar-title>{{ item !== null ? item.title : '' }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>

      <v-card-text class="pdf mt-5 header">
        <p class="pdf_title">СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ</p>
        <div v-if="catalog && catalog.faculty" class="pdf_faculty">
          {{ catalog.faculty }}
        </div>
        <div v-else class="pdf_faculty-line">(назва навчально-наукового інституту/факультету)</div>

        <div v-if="catalog && catalog.department" class="pdf_faculty">
          {{ catalog.department }}
        </div>
        <div v-else class="pdf_faculty-line">(назва кафедри)</div>

        <p class="pdf_subtitle mt-3" v-if="catalog && catalog.speciality && catalog.year && catalog.education_level">
          КАТАЛОГ ВИБІРКОВИХ НАВЧАЛЬНИХ ДИСЦИПЛІН ЦИКЛУ ПРОФЕСІЙНОЇ ПІДГОТОВКИ ЗА СПЕЦІАЛЬНІСТЮ <br />
          {{ catalog.speciality }} <br />
          {{ catalog.education_level }} {{ catalog.year }} &mdash; {{ catalog.year + 1 }} н. р.
        </p>
        <table class="table">
          <thead>
            <tr>
              <th class="text-center" rowspan="2">Назва дисципліни</th>
              <th class="text-center" rowspan="2">Мова викладання</th>
              <th class="text-center" rowspan="2">Кафедра, що пропонує дисципліну</th>
              <th class="text-center" colspan="2">
                Посада, прізвище та ініціали викладача (ів), який (і) пропонується для викладання
              </th>
              <th class="text-center" rowspan="2">
                Компетентності (загальні та/або фахові, на розвиток яких спрямована дисципліна)
              </th>
              <th class="text-center" rowspan="2">Результати навчання за навчальною дисципліною</th>
              <th class="text-center" rowspan="2">Види навчальних занять та методи викладання, що пропонуються</th>
              <th class="text-center" rowspan="2">Кількість здобувачів, які можуть записатися на дисципліну</th>
              <th class="text-center" rowspan="2">
                Вхідні вимоги до здобувачів, які хочуть обрати дисципліну / вимоги до матеріально-технічного
                забезпечення
              </th>
              <th class="text-center" rowspan="2">Обмеження щодо семестру вивчення</th>
              <th class="text-center d-print-none" rowspan="2">Посилання на силабус</th>
              <th class="text-center" rowspan="2">Посилання на НМК дисципліни на платформі Mix</th>
            </tr>
            <tr>
              <th class="text-center">Лекції</th>
              <th class="text-center">Семінарські та практичні заняття, лабораторні роботи</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="subject">
              <td>{{ subject.subject_name }}</td>
              <td>{{ subject.language }}</td>
              <td>{{ subject.department }}</td>
              <td>{{ subject.lecturers }}</td>
              <td>{{ subject.practice }}</td>
              <td>{{ subject.general_competence }}</td>
              <td>{{ subject.learning_outcomes }}</td>
              <td>{{ subject.types_educational_activities }}</td>
              <td class="text-center">{{ subject.number_acquirers }}</td>
              <td>{{ subject.entry_requirements_applicants }}</td>
              <td>{{ subject.limitation }}</td>
              <td><a :href="subject.url" target="_blank"> {{ subject.url }}</a></td>
              <td><a :href="subject.url_mix" target="_blank"> {{ subject.url_mix }}</a></td>
            </tr>
            <tr v-else>
              <td colspan="13" class="text-center">Данні відсутні</td>
            </tr>
          </tbody>
        </table>
        <v-btn class="btn-center" color="primary" dark fixed bottom @click="close">Закрити</v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: 'PreviewSpecialitySubjectModal',
  data() {
    return {
      subject: null,
    };
  },
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      },
    },
    item: null,
    catalog: null,
  },
  mixins: [RolesMixin],
  watch: {
    item(v) {
      if (v !== null && this.dialog === true) {
        this.apiGetItem(v.id);
      }
    },
  },
  methods: {
    apiGetItem(id) {
      api.show(API.SPECIALTY_SUBJECTS, id, { showLoader: true }).then(({ data }) => {
        if (data.data) {
          this.subject = data.data;
        }
      });
    },
    close() {
      this.$emit('close');
    },
  },
};
</script>

<style scoped>
.table {
  width: 100%;
  margin: 40px 0 60px;
  font-size: 15px;
  display: block;
  overflow-x: auto;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 10px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}

.table thead {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.btn-center {
  left: 50%;
  transform: translateX(-50%);
}

.stepper-header {
  height: auto;
}

.header p {
  line-height: 1.6;
}
</style>
