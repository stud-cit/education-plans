<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="500" scrollable>
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon color="red lighten-2" dark v-bind="attrs" v-on="on">
          <v-icon>mdi-email-outline</v-icon>
        </v-btn>
      </template>

      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Зауваження
        </v-card-title>

        <v-card-text>

          <v-list-item three-line v-for="msg in comments" :key="msg.id">
            <v-list-item-content>
              <v-list-item-title>{{ msg.title }}</v-list-item-title>
              <p class="blue--text darken-4">{{ msg.comment }}</p>
            </v-list-item-content>
          </v-list-item>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialog = false">
            Закрити
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>

export default {
  name: 'Messages',
  props: {
    messages: {
      type: Array,
    },
    verifications: {
      type: Array,
    }
  },
  data() {
    return {
      dialog: false,
    }
  },
  computed: {
    comments() {
      return this.messages?.map((item) => {
        const title = this.verifications.find((i) => item.verification_statuses_id == i.id);
        if (title) {
          item.title = title.title;
        }
        return item;
      });
    }
  },
  methods: {
    openDialog() {
      this.dialog = true;
    },
    closeDialog() {
      this.dialog = false;
    },
  }
}
</script>
