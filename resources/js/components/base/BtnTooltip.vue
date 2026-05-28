<template>
  <v-tooltip top>
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot></slot>
      </span>
    </template>
    <span>{{ tooltip }}</span>
  </v-tooltip>
</template>

<script>
export default {
  name: "BtnTooltip",
  props: {
    tooltip: {
      type: String,
      required: true,
      default: ''
    },
    position: {
      type: String,
      default: 'top'
    }
  }
}
</script>

<style scoped>

</style>
