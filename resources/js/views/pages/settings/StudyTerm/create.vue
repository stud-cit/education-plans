<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <validation-observer ref="observer" v-slot="{ invalid }">
          <form @submit.prevent="submit" @keyup.enter="submit">
            <v-card-title>
              <span class="text-h5">Створити термін навчання</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <validation-provider v-slot="{ errors }" name="Назва" rules="required|max:150">
                      <v-text-field
                        v-model="title"
                        :counter="150"
                        :error-messages="errors"
                        label="Назва"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="3">
                    <validation-provider v-slot="{ errors }" name="Роки" rules="required|max:1|max_value:9">
                      <v-text-field
                        v-model="year"
                        :counter="10"
                        type="number"
                        :error-messages="errors"
                        label="Роки"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="3">
                    <validation-provider v-slot="{ errors }" name="Місяць" rules="required|max:2|max_value:12">
                      <v-text-field
                        v-model="month"
                        :counter="2"
                        type="number"
                        :error-messages="errors"
                        label="Місяць"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="2">
                    <validation-provider v-slot="{ errors }" name="Курс" rules="required|max:1|max_value:9">
                      <v-text-field
                        v-model="course"
                        :counter="1"
                        type="number"
                        :error-messages="errors"
                        label="Курс"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="2">
                    <validation-provider v-slot="{ errors }" name="Модуль" rules="required|max:2|max_value:99">
                      <v-text-field
                        v-model="module"
                        :counter="2"
                        type="number"
                        :error-messages="errors"
                        label="Модуль"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="2">
                    <validation-provider v-slot="{ errors }" name="Семестр" rules="required|max:2|max_value:99">
                      <v-text-field
                        v-model="semesters"
                        :counter="2"
                        type="number"
                        :error-messages="errors"
                        label="Семестр"
                        required
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="secondary" @click="close"> Закрити </v-btn>
              <v-btn color="primary" @click="submit" :disabled="invalid"> Зберегти </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </v-card>
    </v-dialog>
  </v-row>
</template>
<script>
export default {
  name: 'CreateStudyTermModal',
  data: () => ({
    title: '',
    year: 1,
    month: 1,
    course: 1,
    module: 1,
    semesters: 1,
  }),
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      },
    },
  },
  methods: {
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          this.$emit('submit', {
            title: this.title,
            year: this.year,
            month: this.month,
            course: this.course,
            module: this.module,
            semesters: this.semesters,
          });
        }
      });
    },
    close() {
      this.title = '';
      this.year = 1;
      this.month = 1;
      this.course = 1;
      this.module = 1;
      this.semesters = 1;
      this.$refs.observer.reset();
      this.$emit('close');
    },
  },
};
</script>
