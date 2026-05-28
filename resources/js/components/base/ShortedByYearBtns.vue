<template>
  <div>
    <!-- Modal Window -->
    <template>
      <v-row justify="center">
        <v-dialog v-model="open" persistent max-width="600px">
          <validation-observer ref="observer" v-slot="{ invalid }">
            <v-card>
              <v-card-title>
                <span class="text-h5">Параметри скороченого плану</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <validation-provider v-slot="{ errors }" name="Скоротити на" rules="required|numeric|digits:1">
                        <v-autocomplete v-model="short" :items="shortTo" :error-messages="errors" label="Скоротити на">
                        </v-autocomplete>
                      </validation-provider>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12">
                      <validation-provider v-slot="{ errors }" name="Рік прийому" rules="required|numeric|digits:4">
                        <v-autocomplete v-model="year" :items="years" :error-messages="errors"
                          label="Рік прийому"></v-autocomplete>
                      </validation-provider>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDialog()"> Закрити </v-btn>
                <v-btn color="blue darken-1" text :disabled="invalid" @click="generateShortedByYear()">
                  Згенерувати
                </v-btn>
              </v-card-actions>
            </v-card>
          </validation-observer>
        </v-dialog>
      </v-row>
    </template>
    <!-- End Modal Window -->

    <div class="d-flex justify-space-between flex-wrap">
      <v-btn v-if="can" class="flex-grow-1 mt-4 mb-2 ml-2" small depressed color="primary" @click="openDialog">
        Згенерувати скорочений план
      </v-btn>
      <template v-for="item in items">
        <v-btn v-if="item.id" :key="item.id" class="flex-grow-1 mt-4 mb-2 ml-2" color="success" small depressed
          :to="{ name: 'EditPlan', params: { id: item.id, title: item.title } }" target="_blank">
          {{ item.label }}
        </v-btn>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ShortedByYearBtns',
  props: {
    items: {
      type: Array,
      required: true,
    },
    planId: {
      required: true,
    },
    can: {
      type: Boolean,
      default: false,
      required: true
    }
  },
  data() {
    return {
      years: this.GlobalFakerYears(),
      year: new Date().getFullYear(),
      shortTo: [1, 2],
      short: null,
      open: false,
    };
  },
  methods: {
    generateShortedByYear() {
      this.$refs.observer.validate().then((valid) => {
        if (!valid) {
          console.error(valid);
        }
        if (valid) {
          this.$store.dispatch('plans/generateShortedByYear', { short: this.short, year: this.year, id: this.planId });
          this.closeDialog();
        }
      })
    },
    openDialog() {
      this.open = true;
    },
    closeDialog() {
      this.open = false;
    },
  },
};
</script>

<style scoped></style>
