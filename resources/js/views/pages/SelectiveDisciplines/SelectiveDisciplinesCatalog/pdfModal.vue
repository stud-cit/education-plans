<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay persistent transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary" class="mb-12">
        <v-toolbar-title>PDF</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon dark @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text id="printMe" class="pdf">
        <p class="pdf_title">
          СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ
        </p>
        <br>
        <p class="pdf_subtitle" v-if="item && 'year' in item">
          ПРОПОЗИЦІЇ ДО КАТАЛОГУ ВИБІРКОВИХ НАВЧАЛЬНИХ ДИСЦИПЛІН ЦИКЛУ ЗАГАЛЬНОЇ ПІДГОТОВКИ на {{ item.year }} &mdash;
          {{ +item.year + 1 }} н. р.
        </p>
        <table class="table pdf_table">
          <thead>
            <tr>
              <th class="text-center" rowspan="2">Назва дисципліни</th>
              <th class="text-center" rowspan="2">Мова викладання</th>
              <th class="text-center" rowspan="2">Рівень освіти, для якого пропонується дисципліна
                (перший/другий/третій)</th>
              <th class="text-center" rowspan="2">Перелік галузей знань / спеціальностей, для яких пропонується
                дисципліна</th>
              <th class="text-center" rowspan="2">Кафедра, що пропонує дисципліну</th>
              <th class="text-center" colspan="2">Посада, прізвище та ініціали викладача (ів), який (і) пропонується для
                викладання</th>
              <th class="text-center" rowspan="2">Загальна компетентність, на формування або розвиток якої спрямована
                дисципліна</th>
              <th class="text-center" rowspan="2">Результати навчання за навчальною дисципліною</th>
              <th class="text-center" rowspan="2">Види навчальних занять та методи викладання, що пропонуються</th>
              <th class="text-center" rowspan="2">Кількість здобувачів, які можуть записатися на дисципліну</th>
              <th class="text-center" rowspan="2">Вхідні вимоги до здобувачів, які хочуть обрати дисципліну / вимоги до
                матеріально-технічного забезпечення</th>
              <th class="text-center" rowspan="2">Обмеження щодо семестру вивчення</th>
              <th class="text-center d-print-none" rowspan="2">Посилання на силабус</th>
              <th class="text-center d-print-none" rowspan="2">Посилання на НМК дисципліни на платформі Mix</th>
            </tr>
            <tr>
              <th class="text-center">Лекції</th>
              <th class="text-center">Семінарські та практичні заняття, лабораторні роботи</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="item && 'group_name' in item">
              <td class="pdf_table-group" colspan="15">
                {{ item.group_name }}
              </td>
            </tr>
            <template v-if="item && 'subjects' in item && item.subjects.length > 0">
              <tr v-for="subject in item.subjects" :key="subject.asu_id">
                <td>{{ subject.subject_name }}</td>
                <td>{{ subject.language }}</td>
                <td>{{ subject.education_level }}</td>
                <td>{{ subject.list_fields_knowledge }}</td>
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
            </template>
            <template v-else>
              <tr>
                <td colspan="15" class="pdf_table-noresult text-center">Данні відсутні</td>
              </tr>
            </template>
          </tbody>
        </table>
        <v-btn class="btn-center no-print" color="primary" dark fixed bottom v-print="'#printMe'">Завантажити</v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import GlobalMixin from "@/mixins/GlobalMixin";
import { ALLOWED_REQUEST_PARAMETERS, API } from "@/api/constants-api";
import api from "@/api";
import print from 'vue-print-nb'
import '@/assets/styles/print.css'


export default {
  name: "PdfSelectiveDisciplinesCatalogModal",
  directives: {
    print
  },
  data() {
    return {
      item: null,
    }
  },
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      }
    },
    options: null,
  },
  watch: {
    dialog(v) {
      if (v === true && this.options !== null) {
        this.apiGetItems();
      }
    }
  },
  methods: {
    apiGetItems() {
      const options = GlobalMixin.methods.GlobalHandlingRequestParameters(
        ALLOWED_REQUEST_PARAMETERS.GET_CATALOG_SELECTIVE_SUBJECTS_PDF,
        this.options,
      );
      api.get(API.CATALOG_SUBJECTS_GENERATE_PDF, options, { showLoader: true }).then(({ data }) => {
        this.item = data.data;
      }).catch(() => this.$emit('close'));
    },
    close() {
      this.$emit('close');
    },
  }
}
</script>

<style scoped>
.table {
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

@media print {
  table tr td:nth-last-child(-n+2) {
    display: none;
  }
}
</style>
