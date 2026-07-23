<script setup lang="ts">
/**
 * Modale per creazione/modifica azienda
 * Usa CompanyForm e gestisce le chiamate API
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useCompanyStore } from "@/stores/useCompanyStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import AppModal from "@/components/common/AppModal.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import CompanyForm from "./CompanyForm.vue";
import type { CompanyCreate } from "@/types/company.types";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const companyStore = useCompanyStore();
const authStore = useAuthStore();
const isAdmin = computed(() => authStore.isAdmin);

interface Props {
  open: boolean;
  companyId?: number | null;
  prefillName?: string;
}

const props = withDefaults(defineProps<Props>(), {
  companyId: null,
  prefillName: "",
});

const emit = defineEmits<{
  close: [];
  saved: [];
}>();

const loading = ref(false);
const saving = ref(false);
const initialData = ref<CompanyCreate | undefined>(undefined);
const isEditing = () => !!props.companyId;
const title = () => (isEditing() ? t("companies.edit") : t("companies.new"));

/**
 * Quando la modale si apre, carica i dati dell'azienda (se edit)
 * oppure prepara il nome precompilato (se create da pending)
 */
watch(
  () => props.open,
  (val) => {
    if (!val) return;
    if (props.companyId && !isAdmin.value) {
      onClose();
      return;
    }
    if (props.companyId) {
      const c = companyStore.currentCompany;
      if (c && c.id === props.companyId) {
        initialData.value = {
          name: c.name,
          sector: c.sector ?? undefined,
          size: c.size as "small" | "medium" | "large" | undefined,
          website: c.website ?? undefined,
          linkedin: c.linkedin ?? undefined,
          description: c.description ?? undefined,
          notes: c.notes ?? undefined,
        };
      }
    } else {
      initialData.value = props.prefillName
        ? {
            name: props.prefillName,
            sector: undefined,
            size: undefined,
            website: undefined,
            linkedin: undefined,
            description: undefined,
            notes: undefined,
          }
        : undefined;
    }
  },
);

/**
 * Chiude la modale senza salvare
 */
function onClose() {
  emit("close");
}

/**
 * Riceve i dati validati dal form, chiama l'API e notifica il parent
 */
async function onFormSubmit(data: CompanyCreate) {
  if (props.companyId && !isAdmin.value) {
    showError(t("common.unauthorized"));
    onClose();
    return;
  }
  saving.value = true;
  try {
    if (props.companyId) {
      await companyStore.updateCompany(props.companyId, data);
      showSuccess(t("notifications.companyUpdated"));
    } else {
      await companyStore.createCompany(data);
      showSuccess(t("notifications.companyCreated"));
    }
    emit("saved");
    onClose();
  } catch (err: any) {
    showError(err.message || t("form.saveError"));
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <AppModal :open="open" :title="title()" max-width="2xl" @close="onClose">
    <AppLoader v-if="loading" :text="t('companies.loadingItem')" />
    <template v-else>
      <CompanyForm
        :initial-data="initialData"
        :disabled="!!props.companyId && !isAdmin"
        @submit="onFormSubmit"
        @cancel="onClose"
      />
    </template>
  </AppModal>
</template>
