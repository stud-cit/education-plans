<template>
  <div class="body">
    <h1 class="big-text">403</h1>
    <p>Доступ заборонено.</p>
      <div class="d-flex align-center ma-10">
        <v-btn
          text
          color="primary"
          href="/" >
          Повернутись на головну
        </v-btn>
        <v-btn
          text
          color="primary"
          href="https://cabinet.sumdu.edu.ua/" >
          Повернутись до кабінету
        </v-btn>
      </div>
    </div>
</template>

<script>
export default {
  name: 'Forbidden',
}
</script>

<style scoped>
.body {
  margin: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.big-text {
  font-size: 56px;
  color: #0c3754;
}
</style>
