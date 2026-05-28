<template>
  <div>
    <v-container>
      <validation-observer :ref="'observer' + item.id" v-slot="{ invalid }" v-for="(item, index) in signatures"
        :key="'signature_' + index">
        <v-row>
          <v-col cols="12" md="2">
            <validation-provider v-slot="{ errors }" name="Посада" rules="required">
              <v-autocomplete v-model="item.position_id" :items="positions" :error-messages="errors" label="Посада"
                data-vv-name="Посада" item-text="position" item-value="id" required
                :disabled="item.edit"></v-autocomplete>
            </validation-provider>
          </v-col>

          <v-col cols="12" md="5">
            <v-text-field v-model="item.manual_position" :items="positions" label="Підрозділ" data-vv-name="Підрозділ"
              item-text="manual_position" item-value="manual_position" :disabled="item.edit"></v-text-field>
          </v-col>

          <v-col cols="12" md="3">
            <v-autocomplete v-model="item.asu_id" :items="workers" label="Посадова особа" data-vv-name="Посадова особа"
              item-text="full_name" item-value="asu_id" :loading="workerLoader" :disabled="item.edit"></v-autocomplete>
          </v-col>

          <v-col cols="12" md="2">
            <div class="text-center pt-3">
              <v-tooltip bottom v-if="item.edit === true">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn :disabled="readOnly || isShortPlan" class="mr-3" v-bind="attrs" v-on="on" outlined fab small
                    color="blue" @click="edit(item)">
                    <v-icon aria-hidden="false"> mdi-pencil </v-icon>
                  </v-btn>
                </template>
                <span>Редагувати</span>
              </v-tooltip>

              <v-tooltip bottom v-if="item.edit === false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn class="mr-3" v-bind="attrs" v-on="on" outlined fab small color="blue"
                    :disabled="invalid || readOnly || isShortPlan" @click="update(item)">
                    <v-icon aria-hidden="false"> mdi-content-save </v-icon>
                  </v-btn>
                </template>
                <span>Оновити</span>
              </v-tooltip>

              <v-tooltip bottom v-if="item.edit === null">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn class="mr-3" v-bind="attrs" v-on="on" outlined fab small color="blue"
                    :disabled="invalid || readOnly || isShortPlan" @click="save(item)">
                    <v-icon aria-hidden="false"> mdi-content-save </v-icon>
                  </v-btn>
                </template>
                <span>Зберегти</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn :disabled="readOnly || isShortPlan" v-bind="attrs" v-on="on" outlined fab small color="red"
                    @click="remove(index, item)">
                    <v-icon aria-hidden="false"> mdi-trash-can-outline </v-icon>
                  </v-btn>
                </template>
                <span>Видалити</span>
              </v-tooltip>
            </div>
          </v-col>
        </v-row>
      </validation-observer>
    </v-container>

    <div class="text-center mt-4" :class="render">
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn :disabled="readOnly || isShortPlan" icon large v-bind="attrs" v-on="on" @click="addItem()">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>Додати позицію</span>
      </v-tooltip>
    </div>
  </div>
</template>
<script>
import api from '@/api';
import { API } from '@/api/constants-api';
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'Signatures',
  data() {
    return {
      positions: [],
      positionLoader: true,
      workers: [],
      workerLoader: true,
    };
  },
  computed: {
    ...mapGetters({
      plan_id: 'plans/id',
      signatures: 'plans/signatures',
      isShortPlan: 'plans/isShortPlan',
      readOnly: 'plans/readOnly',
      isPorject: 'plans/project',
    }),
    render: function () {
      return {
        'd-none': this.isPorject
      }
    },
  },
  mounted() {
    this.getPositions();
    this.apiWorkers();
  },
  methods: {
    ...mapActions({
      addItem: 'plans/addSignature',
    }),
    getPositions() {
      this.apiPositions().then((response) => {
        const { data } = response;
        this.positions = data.data;
        this.positionLoader = false;
      });
    },
    apiPositions() {
      return api.get(API.POSITIONS);
    },
    apiWorkers() {
      api.get(API.LIST_WORKERS).then((response) => {
        const { data } = response;
        this.workers = data;
        this.workerLoader = false;
      });
    },
    save(item) {
      this.$refs['observer' + item.id][0].validate().then((response) => {
        if (response) {
          this.$store.dispatch('plans/saveSignature', item).then(() => {
            this.$swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Дані збережено',
              showConfirmButton: false,
              timer: 1500,
            });
          });
        }
      });
    },
    edit(item) {
      this.$store.dispatch('plans/toggleSignature', item);
    },
    update(item) {
      this.$refs['observer' + item.id][0].validate().then((response) => {
        if (response) {
          this.$store.dispatch('plans/updateSignature', item).then(() => {
            this.$swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Дані оновлено',
              showConfirmButton: false,
              timer: 1500,
            });
          });
        }
      });
    },
    remove(index, item) {
      this.$swal
        .fire({
          title: `Ви хочете видалити елемент?`,
          showDenyButton: true,
          confirmButtonText: 'Так',
          denyButtonText: `Ні`,
        })
        .then((result) => {
          if (result.isConfirmed) {
            this.$store.dispatch('plans/removeSignature', { ...item, index }).then((response) => {
              const { message } = response.data;
              this.$swal.fire({
                position: 'center',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 1500,
              });
            });
          }
        });
    },
  },
};
</script>
<style lang="css" scoped></style>
