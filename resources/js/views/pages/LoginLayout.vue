<template>
  <v-main>
    <v-container fluid fill-height>
      <v-layout align-center justify-center>
        <v-flex xs12 sm8 md4>
          <v-card class="elevation-12">
            <v-toolbar dark color="primary">
              <v-toolbar-title>Авторизація</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
              <v-form>
                <v-text-field
                  v-model="details.email"
                  prepend-icon="mdi-account"
                  name="Пошта"
                  label="Пошта"
                  type="text"
                ></v-text-field>
                <v-text-field
                  v-model="details.password"
                  prepend-icon="mdi-lock-open"
                  name="Пароль"
                  label="Пароль"
                  type="password"
                ></v-text-field>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="onSubmit">Вхід</v-btn>
            </v-card-actions>
          </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-main>
</template>

<script>
import { mapActions} from "vuex";

export default {
  name: "Layout",
  data() {
    return {
      details: {
        email: "",
        password: "",
      },
    }
  },
  methods: {
    ...mapActions("auth", ["sendLoginRequest"]),

    onSubmit() {
      this.sendLoginRequest(this.details).then(() => this.$router.push({name: 'ListPlans'}));
    },
  }
}
</script>

<style scoped>

</style>
