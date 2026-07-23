import { defineStore } from "pinia";
import { ref, computed } from "vue";
import * as applicationsApi from "@/network/applications";
import { useToast } from "@/composables/useToast";
import i18n from "@/plugins/i18n";
import { toCamelCase } from "@/utils/transformers";
import type { Application, ApplicationCreate } from "@/types/application.types";
import type { PaginationMeta, ApiError } from "@/types/shared.types";

export const useApplicationStore = defineStore("application", () => {
  const applications = ref<Application[]>([]);
  const currentApplication = ref<Application | null>(null);
  const loading = ref(false);
  const filters = ref<Record<string, any>>({});
  const pagination = ref<PaginationMeta | null>(null);
  const platformNames = ref<string[]>([]);
  const platformsLoaded = ref(false);
  const { showError } = useToast();

  function handleForbidden(err: any) {
    const error = err as ApiError;
    if (error.status === 403) {
      showError(i18n.global.t("common.unauthorized"));
    }
    throw err;
  }

  /**
   * Restituisce la lista delle candidature
   */
  const applicationsList = computed(() => applications.value);

  /**
   * Recupera lista paginata di candidature dal backend
   * @param page - Numero pagina
   * @param companyId - Filtra per azienda (opzionale)
   */
  async function fetchApplications(page = 1, companyId?: number) {
    loading.value = true;
    try {
      const res = await applicationsApi.getApplications(page, companyId);
      applications.value = res.data.map((a) => toCamelCase<Application>(a));
      pagination.value = res.meta
        ? toCamelCase<PaginationMeta>(res.meta)
        : null;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Recupera una singola candidatura per ID
   */
  async function fetchApplication(id: number) {
    loading.value = true;
    try {
      const res = await applicationsApi.getApplication(id);
      currentApplication.value = toCamelCase<Application>(res.data);
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Crea una nuova candidatura
   */
  async function createApplication(data: ApplicationCreate) {
    loading.value = true;
    try {
      const res = await applicationsApi.createApplication(data);
      const app = toCamelCase<Application>(res.data);
      applications.value.unshift(app);
      platformsLoaded.value = false;
      return app;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Aggiorna una candidatura esistente
   */
  async function updateApplication(
    id: number,
    data: Partial<ApplicationCreate>,
  ) {
    loading.value = true;
    try {
      const res = await applicationsApi.updateApplication(id, data);
      const updated = toCamelCase<Application>(res.data);
      const index = applications.value.findIndex((a) => a.id === id);
      if (index !== -1) applications.value[index] = updated;
      if (currentApplication.value?.id === id)
        currentApplication.value = updated;
      platformsLoaded.value = false;
      return updated;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Elimina una candidatura
   */
  async function deleteApplication(id: number) {
    loading.value = true;
    try {
      await applicationsApi.deleteApplication(id);
      applications.value = applications.value.filter((a) => a.id !== id);
      if (currentApplication.value?.id === id) currentApplication.value = null;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Carica le piattaforme distinte per l'autocomplete.
   * Chiama l'API solo se non ancora caricate.
   */
  async function fetchPlatformNames() {
    if (platformsLoaded.value) return;
    try {
      const res = await applicationsApi.getPlatforms();
      platformNames.value = res.data;
      platformsLoaded.value = true;
    } catch (err: any) {
      handleForbidden(err);
    }
  }

  return {
    applications,
    currentApplication,
    loading,
    filters,
    pagination,
    applicationsList,
    platformNames,
    platformsLoaded,
    fetchApplications,
    fetchApplication,
    createApplication,
    updateApplication,
    deleteApplication,
    fetchPlatformNames,
  };
});
