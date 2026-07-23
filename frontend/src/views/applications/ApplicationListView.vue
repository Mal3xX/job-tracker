<script setup lang="ts">
/**
 * Vista lista candidature con tabella, filtri e paginazione
 */
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useApplicationStore } from "@/stores/useApplicationStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import { useConfirm } from "@/composables/useConfirm";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppSelect from "@/components/common/AppSelect.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppTable from "@/components/common/AppTable.vue";
import AppPagination from "@/components/common/AppPagination.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import ApplicationFormModal from "./components/ApplicationFormModal.vue";
import { formatDate } from "@/utils/formatters";
import { APPLICATION_STATUS } from "@/utils/constants";
import type { Application, ApplicationStatus } from "@/types/application.types";

const { t } = useI18n();
const { showSuccess } = useToast();
const router = useRouter();
const applicationStore = useApplicationStore();
const authStore = useAuthStore();
const searchQuery = ref("");
const statusFilter = ref<string>("");
const sortKey = ref("");
const sortOrder = ref<"asc" | "desc">("asc");
const applicationFormOpen = ref(false);
const { confirm: confirmDialog } = useConfirm();

/**
 * Opzioni per il filtro stato candidatura
 */
const statusOptions = computed(() => [
  { value: "", label: t("applications.filterOptions.allStatuses") },
  {
    value: APPLICATION_STATUS.PENDING,
    label: t("applications.statusLabels.pending"),
  },
  {
    value: APPLICATION_STATUS.NEGATIVE,
    label: t("applications.statusLabels.negative"),
  },
  {
    value: APPLICATION_STATUS.POSITIVE,
    label: t("applications.statusLabels.positive"),
  },
  {
    value: APPLICATION_STATUS.INTERVIEW,
    label: t("applications.statusLabels.interview"),
  },
  {
    value: APPLICATION_STATUS.OFFER,
    label: t("applications.statusLabels.offer"),
  },
  {
    value: APPLICATION_STATUS.NO_RESPONSE,
    label: t("applications.statusLabels.no_response"),
  },
]);

/**
 * Filtra le candidature in base a ricerca e stato
 */
const filteredApplications = computed(() => {
  let result = applicationStore.applications;
  const q = searchQuery.value.toLowerCase().trim();
  if (q) {
    result = result.filter(
      (a) =>
        a.title.toLowerCase().includes(q) ||
        (a.company && a.company.name.toLowerCase().includes(q)) ||
        (a.companyName && a.companyName.toLowerCase().includes(q)) ||
        (a.location && a.location.toLowerCase().includes(q)),
    );
  }
  if (statusFilter.value) {
    result = result.filter((a) => a.status === statusFilter.value);
  }
  return result;
});

/**
 * Indica se almeno un filtro è attivo
 */
const hasActiveFilters = computed(
  () => searchQuery.value !== "" || statusFilter.value !== "",
);

/**
 * Resetta tutti i filtri
 */
function resetFilters() {
  searchQuery.value = "";
  statusFilter.value = "";
}

/**
 * Restituisce label italiana per lo stato candidatura
 */
function getStatusLabel(status: ApplicationStatus): string {
  return t(`applications.statusLabels.${status}`);
}

/**
 * Restituisce variante badge per lo stato candidatura
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
 * Restituisce label italiana per la modalità di lavoro
 */
function getWorkModeLabel(mode: string | null): string {
  if (!mode) return "-";
  return t(`applications.workModeLabels.${mode}`);
}

/**
 * Restituisce il nome dell'azienda o un placeholder
 */
function getCompanyName(app: Application): string {
  return (
    app.company?.name || app.companyName || t("applications.fields.noCompany")
  );
}

const canEditApplication = (app: Application) =>
  authStore.isAdmin || app.userId === authStore.user?.id;

const columns = computed(() => [
  { key: "title", label: t("applications.columns.title"), sortable: true },
  { key: "company", label: t("applications.columns.company"), sortable: false },
  { key: "status", label: t("applications.columns.status"), sortable: true },
  {
    key: "workMode",
    label: t("applications.columns.workMode"),
    sortable: true,
  },
  {
    key: "location",
    label: t("applications.columns.location"),
    sortable: true,
  },
  { key: "createdAt", label: t("applications.columns.date"), sortable: true },
]);

function onPageChange(page: number) {
  applicationStore.fetchApplications(page);
}

function onCreateApplication() {
  applicationFormOpen.value = true;
}

function onViewApplication(app: Application) {
  router.push({ name: "application-detail", params: { id: app.id } });
}

/**
 * Elimina una candidatura dopo conferma
 */
async function onDeleteApplication(app: Application) {
  const confirmed = await confirmDialog({
    title: t("common.confirmDeleteTitle"),
    message: t("common.confirmDelete", { name: app.title }),
    type: "danger",
    confirmText: t("common.delete"),
    cancelText: t("common.cancel"),
  });
  if (!confirmed) return;
  await applicationStore.deleteApplication(app.id);
  showSuccess(t("notifications.applicationDeleted"));
}

onMounted(() => {
  applicationStore.fetchApplications();
});
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">
        {{ t("applications.title") }}
      </h1>
      <AppButton @click="onCreateApplication">{{
        t("applications.new")
      }}</AppButton>
    </div>
    <div class="flex items-end gap-4">
      <div class="flex-1">
        <AppInput
          v-model="searchQuery"
          :placeholder="t('applications.searchPlaceholder')"
        />
      </div>
      <div class="w-48">
        <AppSelect
          v-model="statusFilter"
          :options="statusOptions"
          :placeholder="t('applications.filterOptions.statusPlaceholder')"
        />
      </div>
      <AppButton
        v-if="hasActiveFilters"
        variant="secondary"
        size="sm"
        @click="resetFilters"
      >
        {{ t("common.resetFilters") }}
      </AppButton>
    </div>
    <AppLoader
      v-if="applicationStore.loading"
      :text="t('applications.loading')"
    />
    <template v-else>
      <AppTable
        :columns="columns"
        :data="filteredApplications"
        :sort-key="sortKey"
        :sort-order="sortOrder"
        @sort="(key: string) => (sortKey = key)"
      >
        <template #cell-title="{ row }">
          <button
            class="text-primary-600 hover:text-primary-800 font-medium text-left"
            @click="onViewApplication(row as Application)"
          >
            {{ row.title }}
          </button>
        </template>
        <template #cell-company="{ row }">
          <span class="text-gray-600">{{
            getCompanyName(row as Application)
          }}</span>
        </template>
        <template #cell-status="{ row }">
          <AppBadge :variant="getStatusVariant((row as Application).status)">
            {{ getStatusLabel((row as Application).status) }}
          </AppBadge>
        </template>
        <template #cell-workMode="{ row }">
          <span class="text-gray-600">{{
            getWorkModeLabel(row.workMode)
          }}</span>
        </template>
        <template #cell-location="{ row }">
          <span class="text-gray-500">{{ row.location || "-" }}</span>
        </template>
        <template #cell-createdAt="{ row }">
          <span class="text-gray-500">{{ formatDate(row.createdAt) }}</span>
        </template>
        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <AppButton
              size="sm"
              variant="secondary"
              @click="onViewApplication(row as Application)"
            >
              {{ t("common.view") }}
            </AppButton>
            <AppButton
              v-if="canEditApplication(row as Application)"
              size="sm"
              variant="danger"
              @click="onDeleteApplication(row as Application)"
            >
              {{ t("common.delete") }}
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination
        v-if="applicationStore.pagination"
        :current-page="applicationStore.pagination.currentPage"
        :last-page="applicationStore.pagination.lastPage"
        :total="applicationStore.pagination.total"
        @page-change="onPageChange"
      />
    </template>
    <ApplicationFormModal
      :open="applicationFormOpen"
      @close="applicationFormOpen = false"
      @saved="
        applicationFormOpen = false;
        applicationStore.fetchApplications();
      "
    />
  </div>
</template>
