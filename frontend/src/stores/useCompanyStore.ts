import { defineStore } from "pinia";
import { ref, computed } from "vue";
import * as companiesApi from "@/network/companies";
import { useToast } from "@/composables/useToast";
import i18n from "@/plugins/i18n";
import { toCamelCase } from "@/utils/transformers";
import type { Company, CompanyCreate } from "@/types/company.types";
import type { PaginationMeta, ApiError } from "@/types/shared.types";

export const useCompanyStore = defineStore("company", () => {
  const companies = ref<Company[]>([]);
  const currentCompany = ref<Company | null>(null);
  const companyNames = ref<{ id: number; name: string }[]>([]);
  const namesLoaded = ref(false);
  const loading = ref(false);
  const pagination = ref<PaginationMeta | null>(null);
  const { showError } = useToast();

  function handleForbidden(err: any) {
    const error = err as ApiError;
    if (error.status === 403) {
      showError(i18n.global.t("common.unauthorized"));
    }
    throw err;
  }

  /**
   * Restituisce la lista delle aziende
   */
  const companiesList = computed(() => companies.value);

  /**
   * Cerca un'azienda per ID nella lista caricata
   */
  const companyById = computed(
    () => (id: number) => companies.value.find((c) => c.id === id),
  );

  /**
   * Carica tutti i nomi azienda (id + name) per autocomplete.
   * Chiama l'API solo se non ancora caricati.
   */
  async function fetchCompanyNames() {
    if (namesLoaded.value) return;
    try {
      const res = await companiesApi.getCompanyNames();
      companyNames.value = res.data;
      namesLoaded.value = true;
    } catch (err: any) {
      handleForbidden(err);
    }
  }

  /**
   * Recupera lista paginata di aziende dal backend
   */
  async function fetchCompanies(page = 1) {
    loading.value = true;
    try {
      const res = await companiesApi.getCompanies(page);
      companies.value = res.data.map((c) => toCamelCase<Company>(c));
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
   * Recupera una singola azienda per ID
   */
  async function fetchCompany(id: number) {
    loading.value = true;
    try {
      const res = await companiesApi.getCompany(id);
      currentCompany.value = toCamelCase<Company>(res.data);
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Crea una nuova azienda
   */
  async function createCompany(data: CompanyCreate) {
    loading.value = true;
    try {
      const res = await companiesApi.createCompany(data);
      const company = toCamelCase<Company>(res.data);
      companies.value.unshift(company);
      return company;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Aggiorna un'azienda esistente
   */
  async function updateCompany(id: number, data: Partial<CompanyCreate>) {
    loading.value = true;
    try {
      const res = await companiesApi.updateCompany(id, data);
      const updated = toCamelCase<Company>(res.data);
      const index = companies.value.findIndex((c) => c.id === id);
      if (index !== -1) companies.value[index] = updated;
      if (currentCompany.value?.id === id) currentCompany.value = updated;
      return updated;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Elimina un'azienda
   */
  async function deleteCompany(id: number) {
    loading.value = true;
    try {
      await companiesApi.deleteCompany(id);
      companies.value = companies.value.filter((c) => c.id !== id);
      if (currentCompany.value?.id === id) currentCompany.value = null;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  return {
    companies,
    currentCompany,
    loading,
    pagination,
    companiesList,
    companyById,
    companyNames,
    namesLoaded,
    fetchCompanies,
    fetchCompany,
    createCompany,
    updateCompany,
    deleteCompany,
    fetchCompanyNames,
  };
});
