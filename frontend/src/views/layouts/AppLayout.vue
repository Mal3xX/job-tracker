<script setup lang="ts">
/**
 * Layout principale dell'applicazione con sidebar e header
 */
import TheSidebar from "./TheSidebar.vue";
import TheHeader from "./TheHeader.vue";
</script>

<template>
  <div class="min-h-screen flex bg-gray-50">
    <TheSidebar />
    <div class="flex-1 flex flex-col">
      <TheHeader />
      <main class="flex-1 p-6">
        <router-view :key="$route.fullPath" />
      </main>
    </div>
  </div>
</template>