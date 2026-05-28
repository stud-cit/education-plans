<template>
  <div class="d-flex flex-column justify-space-between flex-grow-1 flex-shrink-1">
    <h1 class="align-self-center big-text mt-15">Технічне обслуговування.</h1>
    <p class="align-self-center">Вибачте за тимчасові незручності.</p>
    <v-btn
      width="300"
      color="primary"
      class="ma-10 mx-auto"
      href="https://cabinet.sumdu.edu.ua/" >
      Повернутись до кабінету
    </v-btn>
    <v-img
      max-width="612"
      max-height="502"
      class="align-self-center image" src="/rocket-launch.png" alt="rocket lunch"> </v-img>
  </div>
</template>
<script>
export default {
  name: 'MaintenanceModePage',
};
</script>

<style scoped>
.big-text {
  font-size: 56px;
  color: #0c3754;
}
</style>
