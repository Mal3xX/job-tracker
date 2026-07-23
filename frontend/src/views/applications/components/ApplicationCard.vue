<script setup lang="ts">
/**
 * Card per visualizzazione candidatura in lista
 * Mostra titolo, azienda, stato e località
 */
import { useI18n } from "vue-i18n";
import AppCard from "@/components/common/AppCard.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import { formatDate } from "@/utils/formatters";
import type { Application, ApplicationStatus } from "@/types/application.types";

const { t } = useI18n();

interface Props {
  application: Application;
}

defineProps<Props>();

/**
 * Label italiana per lo stato candidatura
 */
function getStatusLabel(status: ApplicationStatus): string {
  return t(`applications.statusLabels.${status}`);
}

/**
 * Variante badge per lo stato candidatura
 */
function getStatusVariant(
  status: ApplicationStatus,
): "gray" | "blue" | "green" | "red" | "yellow" | "purple" {
  const map: Record<ApplicationStatus, string> = {
    pending: "yellow",
    negative: "red",
    positive: "green",
    interview: "blue",
    offer: "purple",
    no_response: "gray",
  };
  return (map[status] as any) || "gray";
}

/**
 * Restituisce nome azienda o placeholder
 */
function getCompanyName(app: Application): string {
  return app.company?.name || t("applications.fields.noCompany");
}
</script>

<template>
  <AppCard :hover="true">
    <div class="flex items-start justify-between">
      <div class="space-y-1 flex-1 min-w-0">
        <h3 class="font-medium text-gray-900 truncate">
          {{ application.title }}
        </h3>
        <p class="text-sm text-gray-500">
          {{ getCompanyName(application) }}
          <span v-if="application.location"> · {{ application.location }}</span>
        </p>
        <p class="text-xs text-gray-400 pt-1">
          {{ formatDate(application.createdAt) }}
        </p>
      </div>
      <AppBadge :variant="getStatusVariant(application.status)">
        {{ getStatusLabel(application.status) }}
      </AppBadge>
    </div>
  </AppCard>
</template>
