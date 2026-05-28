<template>
  <v-app>
    <router-view/>
    <loader :show="loading" class="no-print"/>
    <DevelopmentSettings v-if="devtool"/>
    <Helper/>
  </v-app>
</template>

<script>
import {mapGetters, mapState} from "vuex";
import DevelopmentSettings from "@c/Navbar/DevelopmentSettings";
import Loader from "@c/Loader";
import Helper from "@c/Navbar/Helper";

export default {
  name: 'App',
  data: () => ({
    devtool: process.env.VUE_APP_DEBUG === 'true'
  }),
  computed: {
    ...mapGetters(["errors"]),
    ...mapState('loader', ['loading'])
  },

  components: {
    Helper,
    DevelopmentSettings, Loader
  },
  watch: {
    errors() {
      this.showAlert();
    }
  },
  methods: {
    showAlert() {
      if (Object.keys(this.errors).length !== 0) {
        let errors = '';

        for (const messages of Object.values(this.errors)) {
          if (typeof messages !== "string") {
            for (const error of messages) {
              errors += `${error} </br>`
            }
          } else {
            errors += `${messages} </br>`;
          }
        }

        this.$swal.fire({
          icon: 'error',
          title: 'Ой... виникла помилка',
          html: errors,
          confirmButtonText: 'Зачинити',
        }).then(() => {
          this.$store.commit("setErrors", {});
        })
      }
    }
  }
};
</script>
