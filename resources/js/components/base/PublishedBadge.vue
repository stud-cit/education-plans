<template>
  <v-tooltip top color="primary">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on" class="cursor-pointer">
        <v-badge dot :color="getColor(published)" label="published" inline left></v-badge>
      </span>
    </template>
    <span>{{ published ? 'Опубліковано' : 'Не опубліковано' }}</span>
  </v-tooltip>
</template>

<script>
export default {
  name: 'PublishedBadge',
  props: {
    published: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    getColor(published) {
      if (published) return 'green';
      return 'grey';
    },
  },
};
</script>
