<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <validation-observer ref="observer" v-slot="{ invalid }">
          <form @submit.prevent="submit" @keyup.enter="submit">
            <v-card-title>
              <span class="text-h5">Редагувати рівень освіти</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <validation-provider v-slot="{ errors }" name="Назва" rules="required|max:255">
                      <v-text-field
                        v-model="item.title"
                        :counter="255"
                        :error-messages="errors"
                        label="Назва"
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
  name: 'EditEducationLevelModal',
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      },
    },
    item: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  methods: {
    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          this.$emit('submit', { ...this.item });
        }
      });
    },
    close() {
      this.$refs.observer.reset();
      this.$emit('close');
    },
  },
};
</script>
