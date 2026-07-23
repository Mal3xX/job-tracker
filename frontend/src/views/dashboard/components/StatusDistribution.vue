<script setup lang="ts">
/**
 * Grafico a torta distribuzione stati candidature
 */
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { Doughnut } from "vue-chartjs";

const { t } = useI18n();
import { ArcElement, Tooltip, Legend, Chart as ChartJS } from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend);

interface Props {
  data: { status: string; count: number }[];
}

const props = defineProps<Props>();

const colorMap: Record<string, string> = {
  pending: "#f59e0b",
  negative: "#ef4444",
  positive: "#22c55e",
  interview: "#3b82f6",
  offer: "#8b5cf6",
  no_response: "#9ca3af",
};

const chartData = computed(() => ({
  labels: (props.data || []).map((d) => t(`applications.statusLabels.${d.status}`)),
  datasets: [
    {
      data: (props.data || []).map((d) => d.count),
      backgroundColor: (props.data || []).map(
        (d) => colorMap[d.status] || "#6b7280",
      ),
      borderWidth: 0,
    },
  ],
}));

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: "bottom" as const },
  },
};
</script>

<template>
  <div class="card p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">
      {{ t("dashboard.charts.statusDistribution") }}
    </h3>
    <div class="max-w-xs mx-auto">
      <Doughnut :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>
