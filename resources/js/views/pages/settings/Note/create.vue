<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <validation-observer ref="observer" v-slot="{ invalid }">
          <form @submit.prevent="submit" @keyup.enter="submit">
            <v-card-title>
              <span class="text-h5">Додати примітку</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="4">

                    <validation-provider v-slot="{ errors }" name="Опис" rules="required|max:7">

                      <v-text-field v-model="abbreviation" @input="(val) => (abbreviation = abbreviation.toUpperCase())"
                        label="Абревіатура" required :counter="7" :error-messages="errors"></v-text-field>

                    </validation-provider>

                  </v-col>

                  <v-col cols="8">

                    <validation-provider v-slot="{ errors }" name="Опис" rules="required|max:255">
                      <v-text-field v-model="explanation" :counter="255" :error-messages="errors" label="Опис"
                        required></v-text-field>
                    </validation-provider>

                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" sm="6" md="4">
                    <v-menu ref="menu" v-model="menu" :close-on-content-click="false" :return-value.sync="date"
                      transition="scale-transition" offset-y min-width="auto">
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="date" label="Дата" prepend-icon="mdi-calendar" readonly v-bind="attrs"
                          v-on="on"></v-text-field>
                      </template>
                      <v-date-picker v-model="date" no-title scrollable>
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="menu = false">
                          Cancel
                        </v-btn>
                        <v-btn text color="primary" @click="$refs.menu.save(date)">
                          OK
                        </v-btn>
                      </v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="secondary" @click="close">
                Закрити
              </v-btn>
              <v-btn color="primary" @click="submit" :disabled="invalid">
                Зберегти
              </v-btn>
            </v-card-actions>
          </form>
        </validation-observer>
      </v-card>
    </v-dialog>
  </v-row>
</template>
<script>
export default {
  name: 'CreateNoteModal',
  data: () => ({
    abbreviation: '',
    explanation: '',
    date: null,
    menu: false,
  }),
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      }
    },
  },
  methods: {
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          this.$emit('submit', {
            abbreviation: this.abbreviation,
            explanation: this.explanation,
            date: this.date,
          });
          this.close();
        }
      })
    },
    close() {
      this.$refs.observer.reset()
      this.$emit('close')
    }
  }
}
</script>