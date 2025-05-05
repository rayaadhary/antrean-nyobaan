<script setup lang="ts">
import AntrianDisplay from './components/AntrianDisplay.vue'
</script>

<template>
  <div class="app-container">
    <h1 style="color: white;">Sistem Antrean</h1>
    <AntrianDisplay />
  </div>
</template>

<style>
.app-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: Avenir, Helvetica, Arial, sans-serif;
}

h1 {
  color: #2c3e50;
  text-align: center;
  margin-bottom: 30px;
}
</style>