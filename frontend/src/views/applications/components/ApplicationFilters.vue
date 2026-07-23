<script setup lang="ts">
/**
 * Componente filtri per lista candidature
 * Emette l'oggetto filtri al genitore a ogni cambiamento
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import AppInput from "@/components/common/AppInput.vue";
import AppSelect from "@/components/common/AppSelect.vue";
import AppButton from "@/components/common/AppButton.vue";
import { APPLICATION_STATUS, WORK_MODE } from "@/utils/constants";

const { t } = useI18n();

export interface FilterValues {
  search: string;
  status: string;
  companyId: string | number; 
  workMode: string;
  dateFrom: string;
  dateTo: string;
}

interface Props {
  companies?: { id: number; name: string }[];
}

const props = withDefaults(defineProps<Props>(), {
  companies: () => [],
});

const emit = defineEmits<{
  filterChange: [filters: FilterValues];
  reset: [];
}>();

const filters = ref<FilterValues>({
  search: "",
  status: "",
  companyId: "", 
  workMode: "",
  dateFrom: "",
  dateTo: "",
});

/**
 * Opzioni per select stato
 */
const statusOptions = computed(() => [
  { value: "", label: t("applications.filterOptions.allStatuses") },
  { value: APPLICATION_STATUS.PENDING, label: t("applications.statusLabels.pending") },
  { value: APPLICATION_STATUS.NEGATIVE, label: t("applications.statusLabels.negative") },
  { value: APPLICATION_STATUS.POSITIVE, label: t("applications.statusLabels.positive") },
  { value: APPLICATION_STATUS.INTERVIEW, label: t("applications.statusLabels.interview") },
  { value: APPLICATION_STATUS.OFFER, label: t("applications.statusLabels.offer") },
  { value: APPLICATION_STATUS.NO_RESPONSE, label: t("applications.statusLabels.no_response") },
]);

/**
 * Opzioni per select modalità lavoro
 */
const workModeOptions = computed(() => [
  { value: "", label: t("applications.filterOptions.allWorkModes") },
  { value: WORK_MODE.REMOTE, label: t("applications.workModeLabels.remote") },
  { value: WORK_MODE.OFFICE, label: t("applications.workModeLabels.office") },
  { value: WORK_MODE.HYBRID, label: t("applications.workModeLabels.hybrid") },
]);

/**
 * Opzioni per select azienda
 */
const companyOptions = computed(() => [
  { value: "", label: t("applications.filterOptions.allCompanies") },
  ...props.companies.map((c) => ({
    value: c.id,
    label: c.name,
  })),
]);

/**
 * Indica se almeno un filtro è attivo
 */
const hasActiveFilters = ref(false);

watch(
  filters,
  (val) => {
    hasActiveFilters.value =
      val.search !== "" ||
      val.status !== "" ||
      val.companyId !== "" ||
      val.workMode !== "" ||
      val.dateFrom !== "" ||
      val.dateTo !== "";
    emit("filterChange", { ...val });
  },
  { deep: true },
);

/**
 * Resetta tutti i filtri
 */
function resetFilters() {
  filters.value = {
    search: "",
    status: "",
    companyId: "", 
    workMode: "",
    dateFrom: "",
    dateTo: "",
  };
  emit("reset");
}
</script>

<template>
  <div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <AppInput
        v-model="filters.search"
        :placeholder="t('applications.searchPlaceholder')"
      />
      <AppSelect
        v-model="filters.status"
        :options="statusOptions"
        :placeholder="t('applications.filterOptions.statusPlaceholder')"
      />
      <AppSelect
        v-model="filters.companyId"
        :options="companyOptions"
        :placeholder="t('applications.filterOptions.companyPlaceholder')"
      />
      <AppSelect
        v-model="filters.workMode"
        :options="workModeOptions"
        :placeholder="t('applications.filterOptions.workModePlaceholder')"
      />
      <AppInput v-model="filters.dateFrom" type="date" :label="t('applications.filterOptions.dateFrom')" />
      <AppInput v-model="filters.dateTo" type="date" :label="t('applications.filterOptions.dateTo')" />
    </div>
    <div v-if="hasActiveFilters" class="flex justify-end">
      <AppButton variant="secondary" size="sm" @click="resetFilters">
        {{ t("common.resetFilters") }}
      </AppButton>
    </div>
  </div>
</template>
