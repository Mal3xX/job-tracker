<script setup lang="ts">
/**
 * Modale per creazione/modifica candidatura
 * Usa ApplicationForm e gestisce le chiamate API
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useApplicationStore } from "@/stores/useApplicationStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import AppModal from "@/components/common/AppModal.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import ApplicationForm from "./ApplicationForm.vue";
import type { ApplicationCreate } from "@/types/application.types";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const applicationStore = useApplicationStore();
const authStore = useAuthStore();

interface Props {
  open: boolean;
  applicationId?: number | null;
}

const props = withDefaults(defineProps<Props>(), {
  applicationId: null,
});

const emit = defineEmits<{
  close: [];
  saved: [];
}>();

const loading = ref(false);
const saving = ref(false);
const initialData = ref<ApplicationCreate | undefined>(undefined);
const isEditing = computed(() => !!props.applicationId);
const title = () =>
  isEditing.value ? t("applications.edit") : t("applications.new");

const canEditApplication = computed(() => {
  const app = applicationStore.currentApplication;
  if (!app || !isEditing.value) return true;
  return authStore.isAdmin || app.userId === authStore.user?.id;
});

/**
 * Quando la modale si apre, carica i dati della candidatura (se edit)
 */
watch(
  () => props.open,
  (val) => {
    if (!val) return;
    if (isEditing.value && !canEditApplication.value) {
      showError(t("common.unauthorized"));
      emit("close");
      return;
    }
    if (props.applicationId) {
      const a = applicationStore.currentApplication;
      if (a && a.id === props.applicationId) {
        initialData.value = {
          companyName: a.company?.name ?? a.companyName ?? undefined,
          title: a.title,
          workMode: a.workMode ?? undefined,
          location: a.location ?? undefined,
          linkJob: a.linkJob ?? undefined,
          platform: a.platform ?? undefined,
          status: a.status,
          interviewDate: a.interviewDate
            ? a.interviewDate.split("T")[0]
            : undefined,
          salaryMin: a.salaryMin ?? undefined,
          salaryMax: a.salaryMax ?? undefined,
          description: a.description ?? undefined,
          notes: a.notes ?? undefined,
        };
      }
    } else {
      initialData.value = undefined;
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
async function onFormSubmit(data: ApplicationCreate) {
  saving.value = true;
  try {
    if (props.applicationId) {
      const a = applicationStore.currentApplication;
      if (a?.company && data.companyName !== a.company.name) {
        data.companyId = undefined;
      }
      await applicationStore.updateApplication(props.applicationId, data);
      showSuccess(t("notifications.applicationUpdated"));
    } else {
      await applicationStore.createApplication(data);
      showSuccess(t("notifications.applicationCreated"));
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
    <AppLoader v-if="loading" :text="t('applications.loadingItem')" />
    <template v-else>
      <ApplicationForm
        :initial-data="initialData"
        @submit="onFormSubmit"
        @cancel="onClose"
      />
    </template>
  </AppModal>
</template>
