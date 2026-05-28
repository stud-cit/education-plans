<template>
  <v-container>
    <v-dialog v-model="modalVerification.open" max-width="370">
      <v-card>
        <v-card-title class="text-h6"> Причина відхилення верифікації </v-card-title>

        <v-card-text class="pb-0 body-1" v-html="modalVerification.comment"></v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" text @click="modalVerification.open = false"> Закрити </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-if="plan" v-model="programsDialog" persistent max-width="500px">
      <validation-observer ref="observer" v-slot="{ invalid }">
        <form @submit.prevent="verificationOP" @keyup.enter="verificationOP">
          <v-card class="pb-0">
            <v-card-text class="pb-0">
              <v-container>
                <validation-provider v-slot="{ errors }" name="освітня програма" rules="required">
                  <v-autocomplete :items="programs" :loading="programsLoading" v-model="plan.program_op_id"
                    :error-messages="errors" :item-text="getItemText" item-value="program_id" label="Освітня програма"
                    required></v-autocomplete>
                </validation-provider>
              </v-container>
            </v-card-text>
            <v-card-actions class="pt-0">
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="programsDialog = false"> Закрити </v-btn>
              <v-btn color="blue darken-1" text @click="verificationOP()" :loading="verificationOPLoading"
                :disabled="invalid">
                Готово
              </v-btn>
            </v-card-actions>
          </v-card>
        </form>
      </validation-observer>
    </v-dialog>
    <!-- Send to verification with comment -->
    <template>
      <v-row justify="center">
        <v-dialog v-model="notConventionalDialog.dialog" persistent max-width="600px">
          <validation-observer ref="observer" v-slot="{ invalid }">
            <v-card>
              <v-card-title>
                <span class="text-h5">Надрукуйте причину</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <validation-provider v-slot="{ errors }" name="Коментар" rules="required|max:400">
                        <v-textarea type="text" v-model="notConventionalDialog.comment" solo name="input-7-4"
                          :error-messages="errors"></v-textarea>
                      </validation-provider>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="dialogClose()"> Закрити </v-btn>
                <v-btn color="blue darken-1" text :disabled="invalid" @click="sendNotConventionalPlan()">
                  Відправити на верифікацію
                </v-btn>
              </v-card-actions>
            </v-card>
          </validation-observer>
        </v-dialog>
      </v-row>
    </template>

    <template v-if="plan">
      <v-alert outlined dense name="info" type="info" v-if="plan.comment && plan.not_conventional">
        {{ plan.comment }}</v-alert>
    </template>

    <template v-if="plan">
      <v-alert dense name="warning" type="warning" color="red" v-if="plan.actions.forbidden_to_reject_verification">
        Документ оприлюднено в сервісі ОСВІТНІ ПРОГРАМИ! Для скасування верифікації потрібно спочатку скасувати документ
        з публікації. ({{ plan.actions.forbidden_to_reject_verification }}) <br>
        (Термін зняття блокування ~ 2 години)
      </v-alert>
    </template>

    <ShortedByYearBtns v-if="$route.name === 'EditPlan' && plan" :items="plan.shorted_by_year" :plan-id="plan.id"
      :can="plan.actions.can_generate_short_plan" />

    <div class="d-flex align-center flex-wrap gap-1" v-if="$route.name === 'EditPlan' && plan">
      <v-btn small depressed :disabled="disableBtnSendToVerification"
        :color="plan.need_verification === true ? '' : 'success'" @click="actionToVerification()">
        {{ plan.need_verification === true ? 'На верифікації' : 'Відправити на верифікацію' }}
      </v-btn>

      <v-checkbox class="custom-space" v-model="plan.not_conventional" :disabled="hasErrors"
        label="План з особливостями"></v-checkbox>

      <template v-if="plan.short_plan">
        <span class="orange white--text pl-1 pr-1 rounded">Скорочений план</span>
      </template>

      <Messages v-if="verifications && plan.verification_comments" :messages="plan.verification_comments"
        :verifications="verifications" />

      <v-spacer></v-spacer>

      <v-btn v-if="plan.short_plan && plan?.basePlan?.base_id" small depressed color="primary"
        :to="{ name: 'EditPlan', params: { id: plan.basePlan.base_id, title: plan.basePlan.title } }" target="_blank">
        До базового плану
      </v-btn>
      <v-btn small depressed color="primary"
        :to="{ name: 'PreviewPlan', params: { id: plan.id, title: plan.title?.replaceAll('/', '-') } }" target="_blank">
        Переглянути
      </v-btn>
      <!-- Verification buttons -->
      <v-btn small depressed color="success" class="ml-2" v-show="verificationBtn"
        @click="verification({ verification_status_id: authUser.role_id, status: true })">
        Верифікувати
      </v-btn>
      <v-btn small depressed color="error" class="ml-2" v-show="cancelVerificationBtn"
        @click="verification({ verification_status_id: authUser.role_id, status: false })">
        Відхилити верифікацію
      </v-btn>
      <!-- End verification buttons -->
    </div>

    <!-- Verification -->
    <v-stepper elevation="1" class="my-2" v-if="$route.name === 'EditPlan' && plan">
      <v-stepper-header v-if="checkVerification">

        <template v-for="(item, index) in checkVerification">
          <v-stepper-step :key="`${index}-step`" :step="index + 1" :complete="item.status"
            :rules="[() => item.status == null || item.status]"
            :editable="allowedRoles([ROLES.ID.admin, ROLES.ID.root])">
            <span role="button" :class="{ disabled: plan.actions.forbidden_to_reject_verification }" @click="allowedRoles([ROLES.ID.admin, ROLES.ID.root])
              ? verification({ verification_status_id: item.id, status: item.status ? false : true })
              : ''
              ">{{ item.titleHead }}</span>
            <v-btn icon small v-if="item.comment" @click="openDialog(item.comment)" color="error">
              <v-icon small>mdi-bell-ring</v-icon>
            </v-btn>
            <small role="button" :class="{ disabled: plan.actions.forbidden_to_reject_verification }" @click="
              allowedRoles([ROLES.ID.admin, ROLES.ID.root])
                ? verification({ verification_status_id: item.id, status: item.status ? false : true })
                : ''
              ">{{ item.title }}</small>
          </v-stepper-step>
          <v-divider v-if="index != verifications.length - 1" :key="index"></v-divider>
        </template>
      </v-stepper-header>
    </v-stepper>
    <!-- End Verification -->

    <v-row v-if="plan && plan.duplicate_message">
      <v-col>
        <v-alert color="orange" class="mb-2" outlined dense type="warning">
          {{ plan.duplicate_message }}
        </v-alert>
      </v-col>
    </v-row>
    <AlertDuplicate @save="save" @cancel="cancel" v-if="plan && hasDuplicate" :id="plan.id" :hasDuplicate="hasDuplicate"
      :version="version" :plans="dublicatePlans" />

    <v-alert dense outlined type="error" class="mb-2" v-for="(error, errorIndex) in plan.errors"
      :key="'error' + errorIndex">
      {{ error }}
    </v-alert>
    <template v-if="plan.subject_errors && plan.subject_errors.length > 0">
      <v-alert dense outlined type="error" class="mb-2">
        <u>Неправильно розподілено навчальне навантаження за дисципліно:</u>
        <ul>
          <li v-for="item in plan.subject_errors" :key="item.subject_id">
            {{ item.title }}
          </li>
        </ul>
      </v-alert>
    </template>

    <template v-if="errorsPlan && errorsPlan.length > 0">
      <v-alert dense outlined type="error" class="mb-2">
        <ul>
          <li v-for="error in errorsPlan" :key="error.index">
            {{ error }}
          </li>
        </ul>
      </v-alert>
    </template>

    <v-tabs v-model="tab" class="mb-2">
      <v-tab>Загальна інформація</v-tab>
      <v-tab :disabled="$route.name == 'CreatePlan'">Цикли / предмети</v-tab>
      <v-tab :disabled="$route.name == 'CreatePlan'">Графіки</v-tab>
      <v-tab :disabled="signatureDidabled">Підписи</v-tab>
    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item>
        <General @submit="submit" :plan="plan" :edit="$route.name === 'EditPlan'" />
      </v-tab-item>
      <v-tab-item>
        <Cycles v-if="$route.name == 'EditPlan'" @apiGetPlanId="apiGetPlanId" :plan="plan" />
      </v-tab-item>
      <v-tab-item>
        <Title v-if="$route.name == 'EditPlan'" :data="plan" @submit="submit"></Title>
      </v-tab-item>
      <v-tab-item>
        <Signatures />
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>

<script>
import General from '@/views/pages/plan/tabs/General';
import { mapGetters } from 'vuex';
import Title from '@/views/pages/plan/tabs/Title';
import Cycles from '@/views/pages/plan/tabs/Cycles';
import Signatures from '@/views/pages/plan/tabs/Signatures';
import api from '@/api';
import { API } from '@/api/constants-api';
import ShortedByYearBtns from '@c/base/ShortedByYearBtns';
import Messages from '@c/base/Messages';
import AlertDuplicate from '@c/base/AlertDuplicate';
import { ROLES, PLAN_TYPE } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';
import { plan } from '../../../store/modules/plans/getters';

export default {
  name: 'CreatePlan',
  components: {
    General,
    Title,
    Cycles,
    Signatures,
    ShortedByYearBtns,
    Messages,
    AlertDuplicate
  },
  data() {
    return {
      ROLES,
      PLAN_TYPE,
      tab: 0,
      verifications: [],
      programsLoading: false,
      programsDialog: false,
      programs: [],
      modalVerification: {
        open: false,
        comment: '',
      },
      notConventionalDialog: {
        dialog: false,
        comment: '',
      },
      hasDuplicate: false,
      dublicatePlans: [],
      duplicateMessage: '',
      version: 0,
      data: null,
    };
  },
  mixins: [RolesMixin],
  computed: {
    hasErrors() {
      if (this.plan.comment.length > 0) {
        return true;
      } else if (this.plan.errors.length > 0 || this.errorsPlan.length > 0) {
        return false;
      } else {
        return true;
      }
    },
    disableBtnSendToVerification() {
      if (this.plan.not_conventional && this.plan.comment > 0) {
        return true;
      } else if (this.plan.not_conventional && !this.plan.comment) {
        return false;
      } else {
        return (
          this.plan.status == 'success' ||
          !!this.plan.errors.length ||
          !!this.errorsPlan.length ||
          this.plan.need_verification === true
        );
      }
    },

    verificationBtn() {
      if (this.plan.need_verification === false) {
        return false;
      }

      const found = this.verifications.find((element) => {
        if (element.role_id === this.authUser.role_id) {
          return element;
        } else if (element.role_id === ROLES.ID.educational_department_deputy
          && this.authUser.role_id === ROLES.ID.educational_department_chief) {
          return element;
        }
      });

      if (found) {
        return !found.status;
      }

      return [2, 3, 4, 5, 6].indexOf(this.authUser.role_id) != -1;
    },
    cancelVerificationBtn() {
      if (this.plan.actions.forbidden_to_reject_verification) {
        return false;
      }

      if (this.plan.need_verification === false) {
        return false;
      }

      const found = this.verifications.find((element) => {
        if (element.role_id === this.authUser.role_id && element.status == true) {
          return element;
        }
      });

      if (found !== undefined) {
        return found.status;
      }

      return [2, 3, 4, 5, 6].indexOf(this.authUser.role_id) != -1
    },
    checkVerification() {
      return this.verifications.map((element) => {
        let isStatus = this.plan.verification.find((i) => element.id == i.verification_status_id);
        element.titleHead = 'Не перевірено';
        if (isStatus) {
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
    signatureDidabled() {
      if (this.plan.type_id === PLAN_TYPE.PROJECT) {
        return true;
      }
      return this.$route.name == 'CreatePlan';
    },
    ...mapGetters({
      plan: 'plans/plan',
      errorsPlan: 'plans/errorsPlan',
    }),
  },
  mounted() {
    this.start();
  },

  methods: {
    actionToVerification() {
      if (this.plan.not_conventional === false) {
        this.sendToVerification();
      } else {
        this.notConventionalDialog.dialog = true;
      }
    },

    sendNotConventionalPlan() {
      this.$refs.observer.validate().then((valid) => {
        if (!valid) {
          console.error(valid);
        }
        if (valid) {
          this.$store.dispatch('plans/setComment', {
            comment: this.notConventionalDialog.comment,
          });
          this.sendToVerification();
          this.dialogClose();
        }
      });
    },
    dialogClose() {
      this.notConventionalDialog.dialog = false;
      this.notConventionalDialog.comment = '';
    },
    sendToVerification() {
      const data = {
        ...this.plan,
        need_verification: this.plan.need_verification === true ? false : true,
        hours_weeks_semesters: JSON.stringify(this.plan.hours_weeks_semesters),
        schedule_education_process: JSON.stringify(this.plan.schedule_education_process),
        summary_data_budget_time: JSON.stringify(this.plan.summary_data_budget_time),
        practical_training: JSON.stringify(this.plan.practical_training),
      };
      this.submit(data);
    },
    getItemText(item) {
      return `${item.education_program_name}, ${item.year}, ${item.educational_degree}`;
    },

    verification(status) {
      status.user_id = this.authUser.id;
      if (!status.status) {
        this.$swal
          .fire({
            title: 'Введіть причину відхилення верифікації',
            input: 'textarea',
            inputAttributes: {
              autocapitalize: 'off',
            },
            showCancelButton: true,
            confirmButtonText: 'Надіслати',
            cancelButtonText: 'Відміна',
          })
          .then((result) => {
            if (result.isConfirmed) {
              status.comment = result.value;
              this.sendVerification(status);
            }
          });
      } else {
        this.sendVerification(status);
      }
    },

    sendVerification(status) {
      api
        .patch(API.PLAN_VERIFICATION, this.$route.params.id, status)
        .then(() => {
          this.apiGetPlanId();
        })
        .catch((errors) => {
          console.log(errors.response.data);
        });
    },

    openDialog(comment) {
      this.modalVerification.open = true;
      this.modalVerification.comment = comment;
    },
    apiGetSearchDuplicate() {
      const { id, year, speciality_id, education_program_id, study_term_id, type_id } = this.plan;
      if (type_id === PLAN_TYPE.PLAN && (speciality_id || education_program_id)) {
        return this.$store.dispatch('plans/searchDuplicate', {
          'id': id,
          'year': year,
          'speciality_id': speciality_id,
          'education_program_id': education_program_id,
          'study_term_id': study_term_id
        });
      }
    },
    save(message) {
      this.duplicateMessage = message;
      this.plan.duplicate_message = message;
      this.data.duplicate_message = message;
      this.sendRequest(this.data)
    },
    cancel() {
      this.hasDuplicate = false;
    },
    submit(data) {
      this.data = data;
      if (!this.plan.duplicate_message && this.plan.type_id === PLAN_TYPE.PLAN) {
        this.apiGetSearchDuplicate().then((res) => {
          this.hasDuplicate = res.data.hasDuplicate;
          this.version = res.data.version;
          this.dublicatePlans = res.data.plans;
        }).then(() => {
          if (this.hasDuplicate === false) {
            this.sendRequest(data)
          }
        })
      } else {
        this.sendRequest(data)
      }
    },

    sendRequest(data) {
      let path = 'plans/store';

      if (this.$route.name === 'EditPlan') {
        data.id = this.$route.params.id;
        path = 'plans/update';
      }

      this.$store
        .dispatch(path, data)
        .then((response) => {
          const { message } = response.data;
          this.$swal.fire({
            position: 'center',
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500,
          });
          this.$store.dispatch('plans/setSubmitLoading', false);
          if (this.$route.name === 'CreatePlan') {
            this.$router.push({ name: 'EditPlan', params: { id: response.data.id, title: data.title } });
          } else {
            this.apiGetPlanId();
            this.apiGetOptions();
          }

          this.apiGetVerifications();
        })
        .catch(() => {
          this.$store.dispatch('plans/setSubmitLoading', false);
        });
    },

    apiGetPlanId() {
      this.$store.dispatch('plans/show', this.$route.params.id);
    },

    apiGetOptions() {
      this.$store.dispatch('plans/getOptions');
    },
    apiGetVerifications() {
      const type = this.plan.type_id === PLAN_TYPE.PROJECT ? 'project' : 'plan';
      api.get(API.VERIFICATIONS, { type }).then(({ data }) => {
        this.verifications = data.map((item) => {
          item.status = null;
          return item;
        });
      });
    },
    apiGetPrograms() {
      this.programsLoading = true;
      api
        .get(API.PROGRAMS, {
          year: this.plan.year,
          degree: this.plan.education_level.id,
        })
        .then(({ data }) => {
          this.programs = data;
          this.programsLoading = false;
        });
    },
    start() {
      if (this.$route.name === 'EditPlan') {
        this.apiGetPlanId();
        this.apiGetOptions();
      } else {
        this.$store.dispatch('plans/clear');
      }
    },
  },

  watch: {
    $route() {
      this.start();
    },
    plan: {
      handler(newPlan) {
        if (newPlan && newPlan.type_id !== undefined) {
          this.apiGetVerifications();
        }
      },
      immediate: true, // Run the watcher immediately if `plan` already has a value
    },
  },
};
</script>

<style scoped>
.gap-1 {
  gap: 1rem;
}

.disabled {
  pointer-events: none;
  cursor: not-allowed;
}
</style>