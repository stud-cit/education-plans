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
      <!-- cancel verification -->
      <v-dialog v-model="dialogModalVerification" max-width="500">
        <v-card>
          <v-toolbar dark color="primary">
            <v-toolbar-title v-if="modalVerification.status" class="text-h7">
              Введіть причину відхилення верифікації
            </v-toolbar-title>
            <v-toolbar-title v-else class="text-h6"> Причина відхилення верифікації </v-toolbar-title>
          </v-toolbar>
          <v-card-text class="pt-5">
            <v-textarea :readonly="modalVerification.status === false" name="input-6-4" label="Коментар"
              v-model="modalVerification.comment"></v-textarea>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="gray" @click="dialogModalVerification = false"> Закрити </v-btn>
            <v-btn v-if="modalVerification.status !== false" color="primary" dark @click="
              allowedRoles([ROLES.ID.admin, ROLES.ID.root])
                ? verification(modalVerification)
                : setVerification(false, modalVerification.comment)
              ">
              Надіслати
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end cancel verification -->
      <v-card-text class="mt-6 mb-10 d-flex">
        <v-btn small depressed :disabled="checkNeedVerification(subject)"
          v-if="allowedRoles([ROLES.ID.admin, ROLES.ID.root]) || subject.user_id === authUser.id"
          :color="subject.need_verification === true ? '' : 'success'" @click="toggleToVerification()">
          {{ subject.need_verification ? 'На верифікації' : 'Відправити на верифікацію' }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn v-if="showVerificationBtn === null"
          v-show="exceptRoles([ROLES.ID.department, ROLES.ID.practice_department, ROLES.ID.admin, ROLES.ID.root])" small
          depressed color="primary" class="ml-2" @click="setVerification()">
          Верифікувати
        </v-btn>
        <v-btn v-if="showVerificationBtn !== false" small depressed color="error" class="ml-2"
          v-show="exceptRoles([ROLES.ID.department, ROLES.ID.practice_department, ROLES.ID.admin, ROLES.ID.root])"
          @click="cancelVerification()">
          Відхилити верифікацію
        </v-btn>
      </v-card-text>
      <v-card-text class="mb-10">
        <v-stepper elevation="1" v-if="checkVerification">
          <v-skeleton-loader type="list-item-three-line" v-if="checkVerification.length <= 0"></v-skeleton-loader>
          <v-stepper-header v-else class="stepper-header">
            <template v-for="(item, index) in checkVerification">
              <v-stepper-step @click="allowedRoles([ROLES.ID.admin, ROLES.ID.root]) ? openDialog(item) : ''"
                :key="`${index}-step`" :step="index + 1" :complete="item.status"
                :rules="[() => item.status == null || item.status]"
                :editable="allowedRoles([ROLES.ID.admin, ROLES.ID.root])">
                <span>{{ item.titleHead }}</span>

                <v-btn icon small v-if="item.comment" @click="openDialog(item)" color="error">
                  <v-icon small>mdi-bell-ring</v-icon>
                </v-btn>

                <small>{{ item.title }}</small>
              </v-stepper-step>
              <v-divider v-if="index != verifications.length - 1" :key="index"></v-divider>
            </template>
          </v-stepper-header>
        </v-stepper>
      </v-card-text>
      <v-card-text class="pdf">
        <p class="pdf_title">СУМСЬКИЙ ДЕРЖАВНИЙ УНІВЕРСИТЕТ</p>
        <div class="pdf_faculty">
          {{ item.faculty }}
        </div>

        <p class="pdf_subtitle" v-if="item && 'year' in item">
          ДИСЦИПЛІНА ЦИКЛУ ЗАГАЛЬНОЇ ПІДГОТОВКИ на {{ item.year }} – {{ +item.year + 1 }}н. р.
        </p>
        <table class="table">
          <thead>
            <tr>
              <th class="text-center" rowspan="2">Назва дисципліни</th>
              <th class="text-center" rowspan="2">Мова викладання</th>
              <th class="text-center" rowspan="2">
                Рівень освіти, для якого пропонується дисципліна (перший/другий/третій)
              </th>
              <th class="text-center" rowspan="2">
                Перелік галузей знань / спеціальностей, для яких пропонується дисципліна
              </th>
              <th class="text-center" rowspan="2">Кафедра, що пропонує дисципліну</th>
              <th class="text-center" colspan="2">
                Посада, прізвище та ініціали викладача (ів), який (і) пропонується для викладання
              </th>
              <th class="text-center" rowspan="2">
                Загальна компетентність, на формування або розвиток якої спрямована дисципліна
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
              <th class="text-center d-print-none" rowspan="2">Посилання на НМК дисципліни на платформі Mix</th>
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
import { ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: 'PreviewSelectiveDisciplinesCatalogModal',
  data() {
    return {
      ROLES,
      subject: null,
      verifications: [],

      modalVerification: {
        id: null,
        status: null,
        comment: '',
        verification_status_id: null,
      },

      showVerificationBtn: null,
      dialogModalVerification: false,
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
  },
  mixins: [RolesMixin],
  watch: {
    item(v) {
      if (v !== null && this.dialog === true) {
        this.apiGetItem(v.id);
      }
    },
  },
  computed: {
    checkVerification() {
      return this.verifications.map((element) => {
        let isStatus = this.subject.verifications.find((i) => element.id == i.verification_status_id);

        element.titleHead = 'Не перевірено';
        element.verification_status_id = element.id;

        if (isStatus) {
          element.id = isStatus.id;
          element.status = !!isStatus.status;
          element.titleHead = isStatus.status ? 'Верифіковано' : 'Не верифіковано';
          element.comment = isStatus.comment;
        }

        return element;
      });
    },
    authUser() {
      return JSON.parse(localStorage.getItem('user'));
    },
  },
  methods: {
    checkNeedVerification(subject) {
      let status = true;
      if (subject !== null) {
        const { verifications, need_verification } = subject;

        const statuses = verifications.filter((i) => i.status == false);
        status = (need_verification === false && statuses.length > 0) || need_verification;
      }

      return status;
    },
    getVerificationStatusId() {
      const verification_status = this.checkVerification.find((el) => el.role_id === this.authUser.role_id);
      return verification_status?.verification_status_id;
    },
    getShowVerificationBtn() {
      let allow = null;

      if (this.authUser?.role_id && this.subject !== null) {
        const status = this.subject.verifications.find((item) => item.verification_status_id === this.authUser.role_id);

        allow = status?.status ? status.status : null;
      }
      this.showVerificationBtn = allow;
    },
    setVerification(status = true, comment = null) {
      const data = {
        subject_id: this.subject.id,
        verification_status_id: this.getVerificationStatusId(),
        status,
        user_id: this.authUser.id,
      };

      if (!status) {
        data.comment = comment;
      }
      this.sendVerification(data);
    },

    cancelVerification() {
      this.modalVerification = {
        status: null,
        user_id: this.authUser.id,
        comment: null,
      };

      this.dialogModalVerification = true;
    },

    openDialog(item) {
      if (item.status === null) {
        const data = {
          subject_id: this.subject.id,
          verification_status_id: item.id,
          status: true,
          user_id: this.authUser.id,
        };
        this.sendVerification(data);
      } else {
        this.modalVerification = item;
        this.dialogModalVerification = true;
      }
    },

    verification(status) {
      const data = {
        id: status.id,
        subject_id: this.subject.id,
        verification_status_id: status.verification_status_id,
        status: status.status ? false : true,
        user_id: this.authUser.id,
        comment: status.comment,
      };
      this.sendVerification(data);
      this.dialogModalVerification = false;
      data.comment = null;
    },
    sendVerification(status) {
      api
        .patch(API.CATALOG_SELECTIVE_SUBJECTS + '/verification', this.subject.id, status)
        .then(() => {
          this.apiGetItem(this.subject.id);
          this.$emit('init');
          this.dialogModalVerification = false;
        })
        .catch((errors) => {
          this.dialogModalVerification = false;
          console.error(errors.response.data);
        });
    },
    apiGetVerifications() {
      api.get(API.VERIFICATION_SUBJECT_STATUSES).then(({ data }) => {
        this.verifications = data.data;
      });
    },
    apiGetItem(id) {
      api
        .show(API.CATALOG_SELECTIVE_SUBJECTS, id, { showLoader: true })
        .then(({ data }) => {
          if (data.data) {
            this.subject = data.data;
            this.getShowVerificationBtn();
          }
        })
        .then(() => this.apiGetVerifications());
    },
    toggleToVerification() {
      const data = {
        id: this.subject.id,
        need_verification: !this.subject.need_verification,
      };
      api
        .patch(API.CATALOG_SELECTIVE_SUBJECTS + '/toggle-to-verification', this.subject.id, data)
        .then(() => {
          this.apiGetItem(this.subject.id);
        })
        .catch((errors) => {
          console.error(errors.response.data);
        });
    },
    close() {
      this.$emit('close');
    },
    closeModelVerification() {
      this.dialogModalVerification = false;
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
</style>
