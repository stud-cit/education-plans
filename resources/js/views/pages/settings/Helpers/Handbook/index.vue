<template>
  <v-container>
    <validation-observer
      v-if="allowedRoles([ROLES.ID.root])"
      ref="observer"
      v-slot="{ invalid }"
    >
      <form @submit.prevent="submit" @keyup.enter="submit">
        <h2 class="text-center">
          Довідник
        </h2>
        <v-row>
          <v-col cols="12" lg="10">
            <validation-provider
              v-slot="{ errors }"
              name="Файл"
              rules="required|ext:pdf"
            >
              <v-file-input
                v-model="file"
                show-size
                :error-messages="errors"
                truncate-length="15"
                accept="application/pdf"
                label="Завантажити PDF"
                prepend-icon="mdi-file-pdf"
              ></v-file-input>

            </validation-provider>
          </v-col>
          <v-col cols="12" lg="2">
            <btn-tooltip tooltip="Зберегти">
              <v-btn color="success" class="mt-2"  @click="submit" :disabled="invalid">
                Завантажити
                <v-icon right dark>mdi-cloud-upload</v-icon>
              </v-btn>
            </btn-tooltip>
          </v-col>
        </v-row>
      </form>
    </validation-observer>

    <v-progress-linear
      indeterminate
      color="primary"
      v-if="loading"
    ></v-progress-linear>

    <HandbookPdf
      v-if="handbook"
      :pdf="handbook"
    />
    <v-alert
      border="bottom"
      colored-border
      type="warning"
      elevation="2"
      v-if="!handbook && !loading"
    >
      Ой, на цей момент довідник відсутній. Зверніться за допомогою до адміністратора сайту.
    </v-alert>
  </v-container>
</template>

<script>
import api from "@/api";
import {API} from "@/api/constants-api";
import HandbookPdf from "@c/base/HandbookPdf";
import { ROLES } from '@/utils/constants';
import RolesMixin from '@/mixins/RolesMixin';

export default {
  name: "Handbook",
  components: { HandbookPdf },
  mixins: [RolesMixin],

  data: () => {
    return {
      file: null,
      loading: false,
      handbook: null,
      ROLES
    }
  },

  mounted() {
    this.apiGetHandbook();
  },
  methods: {
    apiGetHandbook() {
      this.loading = true;

      api.get(API.UPLOAD_HANDBOOK_PDF + '/index', '', {responseType: 'arraybuffer'})
        .then(response => {
          this.handbook = response.data;
          this.loading = false;
        }).catch(() => {
          this.loading = false;
      })
    },

    submit() {
      this.$refs.observer.validate().then((validated) => {
        if (validated) {
          let formData = new FormData();
          formData.append("doc",  this.file);

          api.post(API.UPLOAD_HANDBOOK_PDF, formData).then( response => {
            const { message } = response.data;
            this.apiGetHandbook();
            this.$swal.fire({
              position: 'center',
              icon: 'success',
              title: message,
              showConfirmButton: false,
              timer: 1500,
            });
          })
        }
      });
    }
  }
}
</script>

<style scoped>

</style>
