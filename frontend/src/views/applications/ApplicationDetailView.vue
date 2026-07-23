<script setup lang="ts">
/**
 * Vista dettaglio candidatura con info e azioni
 */
import { onMounted, ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useApplicationStore } from "@/stores/useApplicationStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import { useConfirm } from "@/composables/useConfirm";
import AppButton from "@/components/common/AppButton.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppCard from "@/components/common/AppCard.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import ApplicationFormModal from "./components/ApplicationFormModal.vue";
import { formatDate, formatCurrency } from "@/utils/formatters";
import type { ApplicationStatus } from "@/types/application.types";

const { t } = useI18n();
const { showSuccess } = useToast();
const route = useRoute();
const router = useRouter();
const applicationStore = useApplicationStore();
const authStore = useAuthStore();
const applicationId = Number(route.params.id);
const applicationFormOpen = ref(false);
const { confirm: confirmDialog } = useConfirm();

const canEditApplication = computed(
  () =>
    authStore.isAdmin ||
    applicationStore.currentApplication?.userId === authStore.user?.id,
);

/**
 * Varianti badge per colore in base allo stato
 */
const statusVariants: Record<ApplicationStatus, string> = {
  pending: "yellow",
  negative: "red",
  positive: "green",
  interview: "blue",
  offer: "purple",
  no_response: "gray",
};

/**
 * Label italiana per modalità lavoro
 */
function getWorkModeLabel(mode: string | null): string {
  if (!mode) return "-";
  return t(`applications.workModeLabels.${mode}`);
}

onMounted(async () => {
  await applicationStore.fetchApplication(applicationId);
});

function onEdit() {
  applicationFormOpen.value = true;
}

/**
 * Elimina la candidatura dopo conferma
 */
async function onDelete() {
  const confirmed = await confirmDialog({
    title: t("common.confirmDeleteTitle"),
    message: t("common.confirmDelete", {
      name: applicationStore.currentApplication?.title,
    }),
    type: "danger",
    confirmText: t("common.delete"),
    cancelText: t("common.cancel"),
  });
  if (!confirmed) return;
  await applicationStore.deleteApplication(applicationId);
  showSuccess(t("notifications.applicationDeleted"));
  router.push({ name: "applications" });
}

function onBack() {
  router.push({ name: "applications" });
}

function onViewCompany() {
  if (applicationStore.currentApplication?.companyId) {
    router.push({
      name: "company-detail",
      params: { id: applicationStore.currentApplication.companyId },
    });
  }
}
</script>

<template>
  <div class="space-y-6">
    <AppLoader
      v-if="applicationStore.loading"
      :text="t('applications.loadingItem')"
    />
    <template v-else-if="applicationStore.currentApplication">
      <!-- Header con titolo e azioni -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button
            class="p-2 rounded-full hover:bg-gray-100 transition-colors"
            @click="onBack"
            :title="t('common.back')"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5 text-gray-600"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ applicationStore.currentApplication.title }}
          </h1>
        </div>
        <div v-if="canEditApplication" class="flex items-center gap-2">
          <AppButton variant="secondary" @click="onEdit">{{
            t("common.edit")
          }}</AppButton>
          <AppButton variant="danger" @click="onDelete">{{
            t("common.delete")
          }}</AppButton>
        </div>
      </div>
      <!-- Info candidatura -->
      <AppCard>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.company") }}
            </h3>
            <p class="mt-1">
              <template v-if="applicationStore.currentApplication.company">
                <button
                  class="text-primary-600 hover:text-primary-800 font-medium"
                  @click="onViewCompany"
                >
                  {{ applicationStore.currentApplication.company.name }}
                </button>
              </template>
              <template
                v-else-if="applicationStore.currentApplication.companyName"
              >
                <span class="text-gray-900">
                  {{ applicationStore.currentApplication.companyName }}
                  <span class="text-xs text-amber-600 ml-1">{{
                    t("applications.fields.pendingCompany")
                  }}</span>
                </span>
              </template>
              <span v-else class="text-gray-400">-</span>
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.status") }}
            </h3>
            <p class="mt-1">
              <AppBadge
                :variant="
                  statusVariants[
                    applicationStore.currentApplication.status
                  ] as any
                "
              >
                {{
                  t(
                    `applications.statusLabels.${applicationStore.currentApplication.status}`,
                  )
                }}
              </AppBadge>
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.workMode") }}
            </h3>
            <p class="mt-1">
              {{
                getWorkModeLabel(applicationStore.currentApplication.workMode)
              }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.location") }}
            </h3>
            <p class="mt-1">
              {{ applicationStore.currentApplication.location || "-" }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.platform") }}
            </h3>
            <p class="mt-1">
              {{ applicationStore.currentApplication.platform || "-" }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.link") }}
            </h3>
            <p class="mt-1">
              <a
                v-if="applicationStore.currentApplication.linkJob"
                :href="applicationStore.currentApplication.linkJob"
                target="_blank"
                class="text-primary-600 hover:text-primary-800"
              >
                {{ applicationStore.currentApplication.linkJob }}
              </a>
              <span v-else class="text-gray-400">-</span>
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.salaryMin") }}
            </h3>
            <p class="mt-1">
              {{
                applicationStore.currentApplication.salaryMin
                  ? formatCurrency(
                      applicationStore.currentApplication.salaryMin,
                    )
                  : "-"
              }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.salaryMax") }}
            </h3>
            <p class="mt-1">
              {{
                applicationStore.currentApplication.salaryMax
                  ? formatCurrency(
                      applicationStore.currentApplication.salaryMax,
                    )
                  : "-"
              }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.interviewDate") }}
            </h3>
            <p class="mt-1">
              {{
                applicationStore.currentApplication.interviewDate
                  ? formatDate(
                      applicationStore.currentApplication.interviewDate,
                    )
                  : "-"
              }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.createdAt") }}
            </h3>
            <p class="mt-1">
              {{ formatDate(applicationStore.currentApplication.createdAt) }}
            </p>
          </div>
          <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.description") }}
            </h3>
            <p class="mt-1 text-gray-700 whitespace-pre-wrap">
              {{
                applicationStore.currentApplication.description ||
                t("applications.fields.noDescription")
              }}
            </p>
          </div>
          <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("applications.fields.notes") }}
            </h3>
            <p class="mt-1 text-gray-700 whitespace-pre-wrap">
              {{
                applicationStore.currentApplication.notes ||
                t("applications.fields.noNotes")
              }}
            </p>
          </div>
        </div>
      </AppCard>
    </template>
    <AppCard v-else>
      <p class="text-gray-500 text-center py-8">
        {{ t("applications.notFound") }}
      </p>
    </AppCard>
    <ApplicationFormModal
      :open="applicationFormOpen"
      :application-id="applicationId"
      @close="applicationFormOpen = false"
      @saved="
        applicationFormOpen = false;
        applicationStore.fetchApplication(applicationId);
      "
    />
  </div>
</template>
