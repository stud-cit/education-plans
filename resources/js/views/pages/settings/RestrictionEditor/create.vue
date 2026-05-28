<template>
  <v-container>
    <validation-observer ref="observer" v-slot="{ invalid }">
      <form @submit.prevent="submit">
        <validation-provider v-slot="{ errors }" name="Ключ" rules="required|max:255">
          <v-text-field v-model="key" :counter="255" :error-messages="errors" label="Ключ" required></v-text-field>
        </validation-provider>

        <validation-provider v-slot="{ errors }" name="Заголовок" rules="required|max:255">
          <v-text-field v-model="title" :counter="255" :error-messages="errors" label="Заголовок"
            required></v-text-field>
        </validation-provider>

        <validation-provider v-slot="{ errors }" name="Значення" rules="required|double,dot">
          <v-text-field v-model="value" :error-messages="errors" label="Значення" required></v-text-field>
        </validation-provider>

        <v-btn class="mr-4" type="submit" :disabled="invalid"> Зберегти </v-btn>
        <v-btn @click="clear"> Очистити </v-btn>
      </form>
    </validation-observer>
  </v-container>
</template>
<script>
import api from "@/api";
import { API } from "@/api/constants-api";
import { required, max, double } from "vee-validate/dist/rules";
import {
  extend,
  ValidationObserver,
  ValidationProvider,
  setInteractionMode,
} from "vee-validate";

setInteractionMode("eager");

extend("double,dot", {
  ...double,
  message: "{_field_} needs to be double. ({_value_})",
});

extend("required", {
  ...required,
  message: "Поле {_field_} не може бути пустим",
});

extend("max", {
  ...max,
  message: "Поле {_field_} не повинно мати більше {length} символів",
});

export default {
  name: "RestrictCreate",
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  data() {
    return {
      key: "",
      title: "",
      value: null,
    };
  },
  methods: {
    submit() {
      this.$refs.observer.validate().then((response) => {
        if (response) {
          let options = {
            key: this.key,
            title: this.title,
            value: this.value,
          };
          api.post(API.SETTINGS, options).then((response) => {
            const { message } = response.data;
            this.$swal.fire({
              position: "center",
              icon: "success",
              title: message,
              showConfirmButton: false,
              timer: 1500,
            });
          });
        }
      });
    },
    clear() {
      this.key = "";
      this.title = "";
      this.value = null;
      this.$refs.observer.reset();
    },
  },
};
</script>
