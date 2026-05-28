<template>
  <div>
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title class="d-flex justify-center">
            <span class="text-h5">Вкажіть причину збереження дублікату</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-alert color="orange" dense outlined prominent shaped type="info">
                    <small>Враховуються такі поля: спеціальність, ОП, рік</small>
                  </v-alert>
                  <h2 class="title mb-2">Перелік планів з такими параметрами:</h2>
                  <p>При натисненні на посилання документ відкриється в новій вкладці</p>
                  <ol class="mb-2">
                    <li v-for="plan in plans" :key="plan.id" class="mb-2 link">
                      <router-link target="_blank"
                        :to="{ name: 'EditPlan', params: { id: plan.id, title: plan.title } }">{{
                          plan.title
                        }}</router-link>
                    </li>
                  </ol>
                  <v-textarea auto-grow row="3" v-model="comment" label="Причина" required></v-textarea>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="closeDialog()">
              Відміна
            </v-btn>
            <v-btn color="blue darken-1" text @click="save()">
              Зберегти
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'AlertDuplicate',
  props: {
    id: {
      required: true,
      type: Number
    },
    hasDuplicate: {
      type: Boolean
    },
    version: {
      type: Number,
    },
    plans: {
      type: Array,
    }
  },
  data() {
    return {
      dialog: true,
      comment: null,

    }
  },
  computed: {
  },
  methods: {
    save() {
      this.$store.dispatch('plans/markAsDuplicate', { id: this.id, duplicate_message: this.comment, version: this.version })
        .then(() => {
          this.closeDialog();
          this.$emit('save', this.comment);
        });
    },
    openDialog() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
      this.$store.dispatch('plans/setSubmitLoading', false);
      this.$emit('cancel');
    },
  }
}</script>

<style scoped>
.link {
  cursor: alias;
}
</style>
