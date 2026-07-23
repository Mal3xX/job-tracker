<script setup lang="ts">
/**
 * Grafico a linee andamento candidature (ultimi 6 mesi)
 */
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler,
} from "chart.js";

const { t } = useI18n();

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler,
);

interface Props {
  data: { month: string; count: number }[];
}

const props = defineProps<Props>();

const chartData = computed(() => ({
  labels: (props.data || []).map((d) => d.month),
  datasets: [
    {
      label: t("dashboard.charts.applications"),
      data: (props.data || []).map((d) => d.count),
      borderColor: "#3b82f6",
      backgroundColor: "rgba(59, 130, 246, 0.1)",
      fill: true,
      tension: 0.4,
    },
  ],
}));

const chartOptions = {
  responsive: true,
  plugins: { legend: { display: false } },
  scales: {
    y: { beginAtZero: true, ticks: { stepSize: 1 } },
  },
};
</script>

<template>
  <div class="card p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">
      {{ t("dashboard.charts.applicationTrend") }}
    </h3>
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>
