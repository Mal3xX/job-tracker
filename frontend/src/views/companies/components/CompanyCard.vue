<script setup lang="ts">
/**
 * Card per visualizzazione azienda in lista
 * Mostra nome, settore, dimensione e link al dettaglio
 */
import { useI18n } from "vue-i18n";
import AppCard from "@/components/common/AppCard.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import type { Company } from "@/types/company.types";

const { t } = useI18n();

interface Props {
  company: Company;
}

defineProps<Props>();
function getSizeLabel(size: string | null): string {
  const map: Record<string, string> = {
    small: t("companies.sizes.small"),
    medium: t("companies.sizes.medium"),
    large: t("companies.sizes.large"),
  };
  return size ? map[size] || size : "-";
}

function getSectorBadgeVariant(
  sector: string | null,
): "gray" | "blue" | "green" | "purple" | "yellow" | "red" {
  if (!sector) return "gray";
  const colors = ["blue", "green", "purple", "yellow", "red"] as const;
  let hash = 0;
  for (let i = 0; i < sector.length; i++) {
    hash = sector.charCodeAt(i) + ((hash << 5) - hash);
  }
  return colors[Math.abs(hash) % colors.length];
}
</script>

<template>
  <AppCard :hover="true">
    <div class="flex items-start justify-between">
      <div class="space-y-1">
        <h3 class="font-medium text-gray-900">{{ company.name }}</h3>
        <p v-if="company.sector" class="text-sm text-gray-500">
          {{ company.sector }}
        </p>
        <p class="text-sm text-gray-400">
          {{ getSizeLabel(company.size) }}
        </p>
      </div>
      <AppBadge
        v-if="company.sector"
        :variant="getSectorBadgeVariant(company.sector)"
      >
        {{ company.sector }}
      </AppBadge>
    </div>
  </AppCard>
</template>
