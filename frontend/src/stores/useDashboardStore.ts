import { defineStore } from "pinia";
import { ref } from "vue";
import { getStats } from "@/network/dashboard";
import { toCamelCase } from "@/utils/transformers";
import type { DashboardStats } from "@/types/dashboard.types";
export const useDashboardStore = defineStore("dashboard", () => {
  const stats = ref<DashboardStats | null>(null);
  const loading = ref(false);
  const dateRange = ref<"month" | "quarter" | "year" | "custom">("month");

  /**
   * Recupera le statistiche della dashboard dal backend
   */
  async function fetchStats() {
    loading.value = true;
    try {
      stats.value = toCamelCase<DashboardStats>(await getStats());
    } catch (err: any) {
      throw err;
    } finally {
      loading.value = false;
    }
  }

  return {
    stats,
    loading,
    dateRange,
    fetchStats,
  };
});
