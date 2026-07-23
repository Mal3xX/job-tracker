import { defineStore } from "pinia";
import { ref } from "vue";
import * as contactsApi from "@/network/contacts";
import { useToast } from "@/composables/useToast";
import i18n from "@/plugins/i18n";
import { toCamelCase } from "@/utils/transformers";
import type { Contact, ContactCreate } from "@/types/contact.types";
import type { ApiError } from "@/types/shared.types";

export const useContactStore = defineStore("contact", () => {
  const contacts = ref<Contact[]>([]);
  const loading = ref(false);
  const { showError } = useToast();

  function handleForbidden(err: any) {
    const error = err as ApiError;
    if (error.status === 403) {
      showError(i18n.global.t("common.unauthorized"));
    }
    throw err;
  }

  /**
   * Recupera la lista dei contatti per una data azienda
   */
  async function fetchContacts(companyId: number) {
    loading.value = true;
    try {
      const res = await contactsApi.getContacts(companyId);
      contacts.value = res.data.map((c) => toCamelCase<Contact>(c));
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Crea un nuovo contatto per un'azienda
   */
  async function createContact(companyId: number, data: ContactCreate) {
    loading.value = true;
    try {
      const res = await contactsApi.createContact(companyId, data);
      const contact = toCamelCase<Contact>(res.data);
      contacts.value.push(contact);
      return contact;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Aggiorna un contatto esistente
   */
  async function updateContact(id: number, data: Partial<ContactCreate>) {
    loading.value = true;
    try {
      const res = await contactsApi.updateContact(id, data);
      const updated = toCamelCase<Contact>(res.data);
      const index = contacts.value.findIndex((c) => c.id === id);
      if (index !== -1) contacts.value[index] = updated;
      return updated;
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Elimina un contatto
   */
  async function deleteContact(id: number) {
    loading.value = true;
    try {
      await contactsApi.deleteContact(id);
      contacts.value = contacts.value.filter((c) => c.id !== id);
    } catch (err: any) {
      handleForbidden(err);
    } finally {
      loading.value = false;
    }
  }

  return {
    contacts,
    loading,
    fetchContacts,
    createContact,
    updateContact,
    deleteContact,
  };
});
