<script setup lang="ts">
/**
 * Lista ultime 5 candidature
 */
import type { Application } from "@/types/application.types";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  applications: Application[];
}

defineProps<Props>();

const statusColor: Record<string, string> = {
  pending: "bg-yellow-100 text-yellow-700",
  negative: "bg-red-100 text-red-700",
  positive: "bg-green-100 text-green-700",
  interview: "bg-blue-100 text-blue-700",
  offer: "bg-purple-100 text-purple-700",
  no_response: "bg-gray-100 text-gray-700",
};
</script>

<template>
  <div class="card p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">
      {{ t("dashboard.recentApplications.title") }}
    </h3>
    <template v-if="applications">
      <div v-if="applications.length === 0" class="text-sm text-gray-500">
        {{ t("dashboard.recentApplications.empty") }}
      </div>
      <div v-else class="space-y-3">
        <div
          v-for="app in applications"
          :key="app.id"
          class="flex items-center justify-between py-2 border-b last:border-b-0"
        >
          <div>
            <router-link
              :to="{ name: 'application-detail', params: { id: app.id } }"
              class="text-sm font-medium text-gray-900 hover:text-primary-600"
            >
              {{ app.title }}
            </router-link>
            <p class="text-xs text-gray-500">
              <router-link
                v-if="app.companyId"
                :to="{ name: 'company-detail', params: { id: app.companyId } }"
                class="hover:text-primary-600"
              >
                {{ app.companyName }}
              </router-link>
              <span v-else>{{
                app.companyName || t("applications.fields.noCompany")
              }}</span>
            </p>
          </div>
          <span
            class="text-xs font-medium px-2 py-1 rounded-full"
            :class="statusColor[app.status] || 'bg-gray-100'"
          >
            {{ t(`applications.statusLabels.${app.status}`) }}
          </span>
        </div>
      </div>
    </template>
  </div>
</template>
