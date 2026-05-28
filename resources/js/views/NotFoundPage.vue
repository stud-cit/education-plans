<template>
  <div>
    <div class="d-flex flex-column justify-center">
      <h1 class="align-self-center big-text">404</h1>
      <p class="align-self-center">Сторінка не знайдена.</p>
      <v-btn
        text
        color="primary"
        class="align-self-center ma-10"
        :to="{ name: 'ListPlans' }" >
        Повернутись на головну
      </v-btn>
    <v-img
        class="align-self-center image"
        src="/rocket-launch.png"
        alt="rocket lunch"
        >
      </v-img>
    </div>
  </div>
</template>
<script>
export default {
  name: 'NotFoundPage'  
}
</script>

<style scoped>
.big-text {
  font-size: 56px;
  color: #0c3754;
}
</style>