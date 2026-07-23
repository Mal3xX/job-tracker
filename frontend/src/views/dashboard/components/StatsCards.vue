<script setup lang="ts">
/**
 * Cards riepilogo con statistiche generali delle candidature
 */
import type { DashboardStats } from "@/types/dashboard.types";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  stats: DashboardStats;
}

const props = defineProps<Props>();

const variazione = computed(() => {
  if (props.stats.previousMonthApplications === 0) {
    return props.stats.currentMonthApplications > 0 ? 100 : 0;
  }
  return Math.round(
    ((props.stats.currentMonthApplications - props.stats.previousMonthApplications) /
      props.stats.previousMonthApplications) *
      100,
  );
});

const cards = computed(() => [
  { label: t("dashboard.cards.totalApplications"), value: props.stats.totalApplications },
  { label: t("dashboard.cards.thisMonth"), value: props.stats.currentMonthApplications },
  { label: t("dashboard.cards.lastMonth"), value: props.stats.previousMonthApplications },
  {
    label: t("dashboard.cards.variation"),
    value: `${variazione.value > 0 ? "+" : ""}${variazione.value}%`,
    highlight: variazione.value > 0,
  },
]);
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div
      v-for="card in cards"
      :key="card.label"
      class="card p-4"
      :class="card.highlight ? 'border-green-200 bg-green-50' : ''"
    >
      <p class="text-sm text-gray-500">{{ card.label }}</p>
      <p
        class="text-2xl font-bold mt-1"
        :class="card.highlight ? 'text-green-600' : 'text-gray-900'"
      >
        {{ card.value }}
      </p>
    </div>
  </div>
</template>
