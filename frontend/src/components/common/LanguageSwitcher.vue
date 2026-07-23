<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { locale } = useI18n();

function toggleLanguage() {
  locale.value = locale.value === "it" ? "en" : "it";
  localStorage.setItem("locale", locale.value);
}
</script>

<template>
  <button
    class="text-xs font-medium px-2 py-1 rounded border border-gray-300 text-gray-500 hover:text-gray-700 hover:border-gray-400 transition-colors"
    @click="toggleLanguage"
  >
    {{ locale === 'it' ? '🇬🇧 EN' : '🇮🇹 IT' }}
  </button>
</template>
