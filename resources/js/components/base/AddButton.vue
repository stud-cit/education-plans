<template>
  <v-tooltip left color="info">
    <template v-slot:activator="{ on, attrs }">
      <v-fab-transition>
        <v-btn color="primary" aria-label="Додати" dark fixed bottom right fab v-bind="attrs" v-on="on" @click="show">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-fab-transition>
    </template>
    <span><slot></slot></span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'AddButton',
  methods: {
    show() {
      this.$emit('show');
    },
  },
};
</script>
