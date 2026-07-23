<script setup lang="ts">
/**
 * Dashboard principale con statistiche e grafici
 */
import { onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useDashboardStore } from "@/stores/useDashboardStore";
import { useToast } from "@/composables/useToast";
import StatsCards from "./components/StatsCards.vue";
import ApplicationsChart from "./components/ApplicationsChart.vue";
import StatusDistribution from "./components/StatusDistribution.vue";
import RecentApplications from "./components/RecentApplications.vue";
import AppLoader from "@/components/common/AppLoader.vue";

const { t } = useI18n();
const { showError } = useToast();
const dashboardStore = useDashboardStore();

onMounted(() => {
  dashboardStore.fetchStats().catch((err: any) => {
    showError(err.message || t("errors.dashboard.fetch"));
  });
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">{{ t("dashboard.title") }}</h1>
    </div>
    <AppLoader
      v-if="dashboardStore.loading"
      :text="t('dashboard.loading')"
    />
    <template v-else-if="dashboardStore.stats">
      <StatsCards :stats="dashboardStore.stats" />
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <ApplicationsChart :data="dashboardStore.stats.trend" />
        <StatusDistribution :data="dashboardStore.stats.statusDistribution" />
      </div>
      <RecentApplications
        :applications="dashboardStore.stats.latestApplications"
      />
    </template>
  </div>
</template>
