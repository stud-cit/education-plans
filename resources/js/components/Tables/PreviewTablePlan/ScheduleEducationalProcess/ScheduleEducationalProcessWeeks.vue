<template>
  <tr class="table-month border-table">
    <td v-for="(week, i) in items" :key="i" class="table-week border-table">
      {{i + 1}}
    </td>
  </tr>
</template>

<script>
export default {
  name: "ScheduleEducationalProcessWeeks",
  props: {
    items: {
      required: true
    }
  }
}
</script>

<style scoped>

</style>
