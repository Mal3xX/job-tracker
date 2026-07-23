<script setup lang="ts">
/**
 * Sidebar di navigazione principale con link alle sezioni
 */
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "@/stores/useAuthStore";

const version = __APP_VERSION__
const route = useRoute();
const { t } = useI18n();
const authStore = useAuthStore();

const navItems = computed(() => {
  const items = [
    {
      name: "dashboard",
      label: t("nav.dashboard"),
      icon: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
    },
    {
      name: "applications",
      label: t("nav.applications"),
      icon: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
    },
    {
      name: "companies",
      label: t("nav.companies"),
      icon: "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4",
    },
  ];
  if (authStore.isAdmin) {
    items.push({
      name: "admin-users",
      label: t("nav.adminUsers"),
      icon: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z",
    });
  }
  return items;
});
</script>

<template>
  <aside class="w-64 bg-white flex flex-col">
    <div class="p-6">
      <div class="flex flex-col items-start gap-1">
        <img src="@/assets/logo.svg" class="h-20 w-auto" alt="JobTracker" />
      </div>
    </div>
    <nav class="flex-1 p-4 space-y-1">
      <router-link
        v-for="item in navItems"
        :key="item.name"
        :to="{ name: item.name }"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
        :class="[
          route.name === item.name
            ? 'bg-primary-50 text-primary-700'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
        ]"
      >
        <svg
          class="w-5 h-5 flex-shrink-0"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            :d="item.icon"
          />
        </svg>
        {{ item.label }}
      </router-link>
    </nav>
    <div class="px-4 py-3 border-t border-gray-100">
      <p class="text-xs text-gray-400 text-center">v{{ version }}</p>
    </div>
  </aside>
</template>
