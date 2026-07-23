<script setup lang="ts">
/**
 * Vista lista aziende con tabella, ricerca e paginazione
 */
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { apiGet } from "@/network/api";
import { useCompanyStore } from "@/stores/useCompanyStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import { useConfirm } from "@/composables/useConfirm";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppTable from "@/components/common/AppTable.vue";
import AppPagination from "@/components/common/AppPagination.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import CompanyFormModal from "./components/CompanyFormModal.vue";
import { formatDate } from "@/utils/formatters";
import type { Company } from "@/types/company.types";
import { toCamelCase } from "@/utils/transformers";

interface PendingCompany {
  companyName: string;
  applicationCount: number;
}

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const { confirm: confirmDialog } = useConfirm();
const router = useRouter();
const companyStore = useCompanyStore();
const authStore = useAuthStore();

function canManage(resource: { creatorId?: number }): boolean {
  return authStore.isAdmin || resource.creatorId === authStore.user?.id;
}
const searchQuery = ref("");
const sortKey = ref("");
const sortOrder = ref<"asc" | "desc">("asc");
const showCompanyModal = ref(false);
const creatingPrefillName = ref("");

/**
 * Filtra le aziende in base alla ricerca testuale
 */
const filteredCompanies = computed(() => {
  if (!searchQuery.value) return companyStore.companies;
  const q = searchQuery.value.toLowerCase();
  return companyStore.companies.filter(
    (c) =>
      c.name.toLowerCase().includes(q) ||
      (c.sector && c.sector.toLowerCase().includes(q)),
  );
});

/**
 * Elenco aziende nominate nelle candidature ma non ancora create.
 * Caricato all'avvio per mostrare il banner di riepilogo.
 */
const pendingCompanies = ref<PendingCompany[]>([]);

onMounted(async () => {
  companyStore.fetchCompanies();
  try {
    const res = await apiGet<{ data: PendingCompany[] }>(
      "/applications/pending-companies",
    );
    pendingCompanies.value = toCamelCase(res.data);
  } catch {}
});

const columns = computed(() => [
  { key: "name", label: t("companies.columns.name"), sortable: true },
  { key: "sector", label: t("companies.columns.sector"), sortable: true },
  { key: "size", label: t("companies.columns.size") },
  {
    key: "createdAt",
    label: t("companies.columns.createdAt"),
    sortable: true,
  },
]);

/**
 * Apre il form di creazione azienda con il nome precompilato.
 * @param name - Nome dell'azienda da creare
 */
function onCreatePending(name: string) {
  creatingPrefillName.value = name;
  showCompanyModal.value = true;
}


/**
 * Restituisce label italiana per la dimensione azienda
 */
function getSizeLabel(size: string | null): string {
  const map: Record<string, string> = {
    small: t("companies.sizes.small"),
    medium: t("companies.sizes.medium"),
    large: t("companies.sizes.large"),
  };
  return size ? map[size] || size : "-";
}

/**
 * Restituisce variante badge in base al settore (consistente per stesso valore)
 */
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

function onPageChange(page: number) {
  companyStore.fetchCompanies(page);
}

function onCreateCompany() {
  creatingPrefillName.value = "";
  showCompanyModal.value = true;
}

function onViewCompany(company: Company) {
  router.push({ name: "company-detail", params: { id: company.id } });
}

/**
 * Elimina un'azienda dopo conferma
 */
async function onDeleteCompany(company: Company) {
  const confirmed = await confirmDialog({
    title: t("common.confirmDeleteTitle"),
    message: t("common.confirmDelete", { name: company.name }),
    type: "danger",
    confirmText: t("common.delete"),
    cancelText: t("common.cancel"),
  });
  if (!confirmed) return;
  try {
    await companyStore.deleteCompany(company.id);
    showSuccess(t("notifications.companyDeleted"));
  } catch (err: any) {
    if (err.status !== 403) {
      showError(err.message || t("common.unauthorized"));
    }
  }
}

/**
 * Ricarica la lista aziende dopo un salvataggio riuscito
 */
function onCompanySaved() {
  companyStore.fetchCompanies();
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">
        {{ t("companies.title") }}
      </h1>
      <AppButton @click="onCreateCompany">{{ t("companies.new") }}</AppButton>
    </div>
    <AppInput
      v-model="searchQuery"
      :placeholder="t('companies.searchPlaceholder')"
    />
    <div
      v-if="pendingCompanies.length > 0"
      class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6"
    >
      <h3 class="text-sm font-medium text-amber-800">
        {{ t("companies.pending.title", { count: pendingCompanies.length }) }}
      </h3>
      <ul class="mt-2 space-y-1">
        <li
          v-for="pc in pendingCompanies"
          :key="pc.companyName"
          class="flex items-center justify-between text-sm"
        >
          <span class="text-amber-700">
            {{ pc.companyName }}
            ({{
              t("companies.pending.applicationsCount", {
                count: pc.applicationCount,
              })
            }})
          </span>
          <AppButton size="sm" @click="onCreatePending(pc.companyName)">
            {{ t("companies.pending.createButton") }}
          </AppButton>
        </li>
      </ul>
    </div>
    <AppLoader v-if="companyStore.loading" :text="t('companies.loading')" />
    <template v-else>
      <AppTable
        :columns="columns"
        :data="filteredCompanies"
        :sort-key="sortKey"
        :sort-order="sortOrder"
        @sort="(key: string) => (sortKey = key)"
      >
        <template #cell-name="{ row }">
          <button
            class="text-primary-600 hover:text-primary-800 font-medium"
            @click="onViewCompany(row as Company)"
          >
            {{ row.name }}
          </button>
        </template>
        <template #cell-sector="{ row }">
          <AppBadge
            v-if="row.sector"
            :variant="getSectorBadgeVariant(row.sector)"
          >
            {{ row.sector }}
          </AppBadge>
          <span v-else class="text-gray-400">-</span>
        </template>
        <template #cell-size="{ row }">
          <span class="text-gray-600">{{ getSizeLabel(row.size) }}</span>
        </template>
        <template #cell-createdAt="{ row }">
          <span class="text-gray-500">{{ formatDate(row.createdAt) }}</span>
        </template>
        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <AppButton
              size="sm"
              variant="secondary"
              @click="onViewCompany(row as Company)"
            >
              {{ t("common.view") }}
            </AppButton>
            <AppButton
              v-if="canManage(row as Company)"
              size="sm"
              variant="danger"
              @click="onDeleteCompany(row as Company)"
            >
              {{ t("common.delete") }}
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination
        v-if="companyStore.pagination"
        :current-page="companyStore.pagination.currentPage"
        :last-page="companyStore.pagination.lastPage"
        :total="companyStore.pagination.total"
        @page-change="onPageChange"
      />
    </template>
    <CompanyFormModal
      :open="showCompanyModal"
      :prefill-name="creatingPrefillName"
      @close="showCompanyModal = false"
      @saved="onCompanySaved"
    />
  </div>
</template>
