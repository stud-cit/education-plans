<template>
  <div>
    <div class="d-flex flex-column justify-center">
    <h1 class="align-self-center big-text">401</h1>
    <p class="align-self-center">Доступ до сайту заборонено.</p>
    <v-btn
        text
        color="primary"
        class="align-self-center ma-10"
        href="https://cabinet.sumdu.edu.ua/">
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
  name: 'Unauthorized',
}
</script>

<style scoped>
.big-text {
  font-size: 56px;
  color: #0c3754;
}
</style>
