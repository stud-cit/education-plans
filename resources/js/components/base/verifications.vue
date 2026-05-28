<template>
  <div>
    <v-dialog v-model="dialogModalVerification" max-width="500">
      <v-card>
        <v-toolbar dark color="primary">
          <v-toolbar-title v-if="modalVerification.status" class="text-h7">
            Введіть причину відхилення верифікації
          </v-toolbar-title>
          <v-toolbar-title v-else class="text-h6"> Причина відхилення верифікації </v-toolbar-title>
        </v-toolbar>
        <v-card-text class="pt-5">
          <v-textarea
            :readonly="modalVerification.status === false"
            name="input-6-4"
            label="Коментар"
            v-model="modalVerification.comment"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="gray" @click="dialogModalVerification = false"> Закрити </v-btn>
          <v-btn
            v-if="modalVerification.status !== false"
            color="primary"
            dark
            @click="
                allowedRoles([ROLES.ID.admin, ROLES.ID.root])
                  ? verification(modalVerification)
                  : setVerification(false, modalVerification.comment)
              "
          >
            Надіслати
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-card-text class="d-flex">
      <v-btn
        small
        depressed
        :disabled="checkNeedVerification(item)"
        v-if="allowedRoles([ROLES.ID.admin, ROLES.ID.root]) || item.toggle_to_verification"
        :color="item.need_verification === true ? '' : 'success'"
        @click="toggleToVerification()"
      >
        {{ item.need_verification ? 'На верифікації' : 'Відправити на верифікацію' }}
      </v-btn>
      <v-spacer></v-spacer>
      <template v-if="item.can_verification && item.need_verification">
        <v-btn
          v-if="showVerificationBtn === null"
          v-show="exceptRoles([ROLES.ID.department, ROLES.ID.practice_department, ROLES.ID.admin, ROLES.ID.root])"
          small
          depressed
          color="primary"
          class="ml-2"
          @click="setVerification()"
        >
          Верифікувати
        </v-btn>
        <v-btn
          v-if="showVerificationBtn !== false"
          small
          depressed
          color="error"
          class="ml-2"
          v-show="exceptRoles([ROLES.ID.department, ROLES.ID.practice_department, ROLES.ID.admin, ROLES.ID.root])"
          @click="cancelVerification()"
        >
          Відхилити верифікацію
        </v-btn>
      </template>
    </v-card-text>
    <v-card-text class="mb-10">
      <v-stepper elevation="1" v-if="checkVerification">
        <v-skeleton-loader type="list-item-three-line" v-if="checkVerification.length <= 0"></v-skeleton-loader>
        <v-stepper-header v-else class="stepper-header">
          <template v-for="(item, index) in checkVerification">
            <v-stepper-step
              @click="allowedRoles([ROLES.ID.admin, ROLES.ID.root]) ? openDialog(item) : ''"
              :key="`${index}-step`"
              :step="index + 1"
              :complete="item.status"
              :rules="[() => item.status == null || item.status]"
              :editable="allowedRoles([ROLES.ID.admin, ROLES.ID.root])"
            >
              <span>{{ item.titleHead }}</span>

              <v-btn icon small v-if="item.comment" @click="openDialog(item)" color="error">
                <v-icon small>mdi-bell-ring</v-icon>
              </v-btn>

              <small>{{ item.title }}</small>
            </v-stepper-step>
            <v-divider v-if="index != verificationsList.length - 1" :key="index"></v-divider>
          </template>
        </v-stepper-header>
      </v-stepper>
    </v-card-text>
  </div>
</template>

<script>
import { ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: "verifications",
  data() {
    return {
      ROLES,
      modalVerification: {
        id: null,
        status: null,
        comment: '',
        verification_status_id: null,
      },
      showVerificationBtn: null,
      dialogModalVerification: false,
    }
  },
  props: {
    item: {
      type: Object,
      default: null,
    },
    verificationsList: null,
  },
  mixins: [RolesMixin],
  computed: {
    checkVerification() {
      if (this.verificationsList !== null) {
        return this.verificationsList.map((element) => {
          let isStatus = this.item.verifications.find((i) => element.id == i.verification_status_id);

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
      } else {
        return [];
      }
    },
    authUser() {
      return JSON.parse(localStorage.getItem('user'));
    },
  },
  methods: {
    checkNeedVerification(item) {
      let status = true;
      if (item !== null) {
        const { verifications, need_verification } = item;

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

      if (this.authUser?.role_id && this.item !== null) {
        const status = this.item.verifications.find((item) => item.verification_status_id === this.authUser.role_id);

        allow = status?.status ? status.status : null;
      }
      this.showVerificationBtn = allow;
    },
    setVerification(status = true, comment = null) {
      const data = {
        catalog_id: this.item.id,
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
          catalog_id: this.item.id,
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
        catalog_id: this.item.id,
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
      this.$emit('submit', status);
      this.dialogModalVerification = false;
    },

    toggleToVerification() {
      const data = {
        id: this.item.id,
        need_verification: !this.item.need_verification,
      };

      this.$emit('toggleVerification', data);
    },
    closeModelVerification() {
      this.dialogModalVerification = false;
    },
  }
}
</script>

<style scoped>
  .stepper-header {
    height: auto;
  }
</style>
