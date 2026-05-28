<template>
  <v-card elevation="0">
    <v-toolbar
      elevation="0"
      class="mb-12"
    >
      <v-toolbar-title>PDF Спеціальності</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <div id="printMe" v-if="items.length !== 0">
      <v-card-text class="pdf" v-for="item in items" :key="item.catalog.id">
        <p class="pdf_title">
          СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ
        </p>
        <div v-if="item.catalog" class="pdf_faculty">
          {{item.catalog.faculty}}
        </div>
        <div v-else class="pdf_faculty-line">
          (назва навчально-наукового інституту/факультету)
        </div>

        <div v-if="item.catalog" class="pdf_faculty">
          {{item.catalog.department}}
        </div>
        <div v-else class="pdf_faculty-line">
          (назва кафедри)
        </div>

        <p class="pdf_subtitle mt-3" v-if="item.catalog">
          КАТАЛОГ ВИБІРКОВИХ НАВЧАЛЬНИХ ДИСЦИПЛІН ЦИКЛУ ПРОФЕСІЙНОЇ ПІДГОТОВКИ ЗА СПЕЦІАЛЬНІСТЮ <br>
          {{item.catalog.speciality}} <br>
          {{item.catalog.education_level}} {{item.catalog.year}} &mdash; {{item.catalog.year + 1}} н. р.
        </p>
        <table class="table pdf_table">
          <thead>
          <tr>
            <th class="text-center" rowspan="2">Назва дисципліни</th>
            <th class="text-center" rowspan="2">Мова викладання</th>
            <th class="text-center" rowspan="2">Кафедра, що пропонує дисципліну</th>
            <th class="text-center" colspan="2">Посада, прізвище та ініціали викладача (ів), який (і) пропонується для викладання</th>
            <th class="text-center" rowspan="2">Компетентності (загальні та/або фахові,  на розвиток яких спрямована дисципліна</th>
            <th class="text-center" rowspan="2">Результати навчання за навчальною дисципліною</th>
            <th class="text-center" rowspan="2">Види навчальних занять та методи викладання, що пропонуються</th>
            <th class="text-center" rowspan="2">Кількість здобувачів, які можуть записатися на дисципліну</th>
            <th class="text-center" rowspan="2">Вхідні вимоги до здобувачів, які хочуть обрати дисципліну / вимоги до матеріально-технічного забезпечення</th>
            <th class="text-center" rowspan="2">Обмеження щодо семестру вивчення</th>
          </tr>
          <tr>
            <th class="text-center">Лекції</th>
            <th class="text-center">Семінарські та практичні заняття, лабораторні роботи</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td class="text-center" v-for="number in 11" :key="number">{{number}}</td>
          </tr>
          <tr v-if="item && 'group_name' in item">
            <td class="pdf_table-group" colspan="12">
              {{item.group_name}}
            </td>
          </tr>
          <template v-if="item && 'subjects' in item && item.subjects.length > 0">
            <tr v-for="subject in item.subjects" :key="subject.asu_id">
              <td>{{ subject.subject_name}}</td>
              <td>{{ subject.language }}</td>
              <td>{{ subject.department }}</td>
              <td>{{ subject.lecturers }}</td>
              <td>{{ subject.practice }}</td>
              <td>{{ subject.general_competence }}</td>
              <td>{{ subject.learning_outcomes }}</td>
              <td>{{ subject.types_educational_activities }}</td>
              <td  class="text-center">{{ subject.number_acquirers }}</td>
              <td>{{ subject.entry_requirements_applicants }}</td>
              <td>{{ subject.limitation }}</td>
            </tr>
          </template>
          <template v-else>
            <tr>
              <td colspan="12" class="pdf_table-noresult text-center">Данні відсутні</td>
            </tr>
          </template>
          </tbody>
        </table>
        <p class="pdf_subscribe">
          За всіма вказаними навчальними дисциплінами розроблені повні комплекси навчально-методичного забезпечення.
        </p>

        <v-row class="pdf__signatures">
          <v-col cols="4" class="left">
            <div class="pdf__signature">
              <v-row>
                <v-col>
                  <div class="title">
                    Голова Ради з якості інституту (факультету)
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <div class="field">
                    <template v-if="getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.head.id)">
                      {{getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.head.id).faculty}}
                    </template>
                    <span v-else>(абревіатура інституту (факультету))</span>
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <div class="field">
                    <span>(підпис)</span>
                  </div>
                </v-col>
                <v-col>
                  <div class="field">
                    <template v-if="getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.head.id)">
                      {{getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.head.id).name}}
                    </template>
                    <span v-else>(ім’я та прізвище)</span>
                  </div>
                </v-col>
              </v-row>
            </div>
          </v-col>
          <v-col cols="4" class="right">
            <v-row>
              <v-col class="agreed">
                ПОГОДЖЕНО:
              </v-col>
            </v-row>
            <div class="pdf__signature">
              <v-row>
                <v-col>
                  <div class="title">
                    Керівник групи забезпечення спеціальності
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <div class="field">
                    <span>(підпис)</span>
                  </div>
                </v-col>
                <v-col>
                  <div class="field">
                    <template v-if="getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.leader.id)">
                      {{getNameSignature(item.signatures, CATALOG_SIGNATURE_TYPE.leader.id).name}}
                    </template>
                    <span v-else>(ім’я та прізвище)</span>
                  </div>
                </v-col>
              </v-row>
            </div>
            <div class="pdf__signature"
                 v-for="manager in item.signatures.filter(el => el.type === CATALOG_SIGNATURE_TYPE.manager.id)"
                 :key="manager.id"
            >
              <v-row>
                <v-col cols="5">
                  <div class="title">
                    Завідувач кафедри
                  </div>
                </v-col>
                <v-col>
                  <div class="field">
                    <template v-if="manager.department">
                      {{manager.department}}
                    </template>
                    <span v-else>(абревіатура кафедри)</span>
                  </div>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <div class="field">
                    <span>(підпис)</span>
                  </div>
                </v-col>
                <v-col>
                  <div class="field">
                    <template v-if="manager.name">
                      {{manager.name}}
                    </template>
                    <span v-else>(ім’я та прізвище)</span>
                  </div>
                </v-col>
              </v-row>
            </div>
          </v-col>
        </v-row>
      </v-card-text>
      <div style="page-break-before:always">&nbsp;</div>
      <v-btn
        class="btn-center no-print"
        color="primary"
        dark
        fixed
        bottom
        v-print="'#printMe'"
      >Завантажити</v-btn>
    </div>
    <div v-else class="d-flex align-center justify-center flex-grow-1 flex-shrink-1">
      <h2>Каталогів не знайдено</h2>
    </div>
  </v-card>
</template>

<script>
import { API } from "@/api/constants-api";
import { CATALOG_SIGNATURE_TYPE } from "@/utils/constants"
import api from "@/api";
import print from 'vue-print-nb'
import '@/assets/styles/print.css'

export default {
  name: "CatalogSpecialityPDF",
  directives: {
    print
  },
  data() {
    return {
      items: [],
      CATALOG_SIGNATURE_TYPE
    }
  },
  mounted() {
    this.apiGetItems();
  },
  methods: {
    apiGetItems() {
      const data = {
        id: this.$route.params.id,
        ...this.$route.query
      }
      api.get(API.PLANS_CATALOG_PDF, data, { showLoader: true }).then( ({data}) => {
        this.items = data;
      });
    },
    getNameSignature(signatures, type) {
      return signatures.find(el => el.type === type)
    }
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
.table th, .table td {
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
</style>
