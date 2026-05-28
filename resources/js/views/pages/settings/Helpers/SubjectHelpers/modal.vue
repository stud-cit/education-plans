<template>
  <v-row justify="center">
    <v-dialog
      v-model="dialog"
      persistent
      max-width="800px"
    >
      <v-card>
        <validation-observer
          ref="observer"
          v-slot="{ invalid }"
        >
          <form @submit.prevent="submit" @keyup.enter="submit">
            <v-card-title>
              <span class="text-h5">{{item === null ? "Створити" : "Редагувати"}} підсказку</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <validation-provider
                  v-slot="{ errors }"
                  name="Тип для підсказки"
                  rules="required"
                  vid="title"
                >
                  <v-autocomplete
                    v-model="type"
                    :items="types"
                    :error-messages="errors"
                    item-text="title"
                    item-value="id"
                    label="Тип для підсказки"
                  ></v-autocomplete>
                </validation-provider>
                <validation-provider
                  v-slot="{ errors }"
                  name="Назва підсказки"
                  vid="title"
                  rules="required"
                >
                  <v-textarea
                    v-model="title"
                    name="title"
                    label="Назва підсказки"
                    :error-messages="errors"
                  ></v-textarea>
                </validation-provider>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="secondary"
                @click="close"
              >
                Закрити
              </v-btn>
              <v-btn
                color="primary"
                @click="item === null ? submit() : update()"
                :disabled="invalid"
              >
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
  name: "CreateOrUpdateModalSubjectHelpers",
  data() {
    return {
      type: null,
      title: ''
    }
  },
  props: {
    dialog: {
      type: Boolean,
      default() {
        return false;
      }
    },
    types: [],
    item: {
      type: Object,
      default: () => null,
    },
  },
  watch: {
    item(v) {
      if (v !== null && v.title) {
        this.title = v.title
      }
      if (v !== null && v.type) {
        this.type = v.type
      }
    },
  },
  methods: {
    submit() {
      this.$emit('submit', {
        title: this.title,
        type: this.type
      });
    },
    update() {
      this.$emit('update', {
        id: this.item.id,
        title: this.title,
        type: this.type.id ? this.type.id : this.type
      });
    },
    close() {
      this.$refs.observer.reset();
      this.$emit('close');
      this.type = null;
      this.title= '';
    },
    setErrors(errors) {
      this.$refs.observer.setErrors(
        errors
      );
    }
  }
}
</script>

<style scoped>

</style>
