<template>
  <tr class="table-month border-table">
    <td rowspan="2">
      Курс
    </td>
    <td v-for="(month, index) in items"
        :key="index" :colspan="month.countWeek"
        class="border-table"
    >
      {{ month.monthTitle }}
    </td>
  </tr>
</template>

<script>
export default {
  name: "ScheduleEducationalProcessMonth",
  props: {
    items: {
      required: true
    }
  }
}
</script>

<style scoped>

</style>
