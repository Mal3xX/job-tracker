<script setup lang="ts">
/**
 * Modale per creazione/modifica contatto aziendale
 * Wrapper che usa ContactForm e gestisce le chiamate API
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useContactStore } from "@/stores/useContactStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import AppModal from "@/components/common/AppModal.vue";
import ContactForm from "./ContactForm.vue";
import type { Contact, ContactCreate } from "@/types/contact.types";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const contactStore = useContactStore();
const authStore = useAuthStore();
const isAdmin = computed(() => authStore.isAdmin);

interface Props {
  open: boolean;
  companyId: number;
  contact?: Contact | null;
}

const props = withDefaults(defineProps<Props>(), {
  contact: null,
});

const emit = defineEmits<{
  close: [];
  saved: [];
}>();

const saving = ref(false);
const initialData = ref<ContactCreate | null>(null);
const isEditing = () => !!props.contact;
const title = () =>
  isEditing() ? t("companies.contacts.edit") : t("companies.contacts.new");

/**
 * Quando la modale si apre, prepara i dati iniziali del form
 */
watch(
  () => props.open,
  (val) => {
    if (val) {
      initialData.value = props.contact
        ? {
            companyId: props.companyId,
            name: props.contact.name,
            email: props.contact.email ?? undefined,
            phone: props.contact.phone ?? undefined,
            role: props.contact.role ?? undefined,
            linkedin: props.contact.linkedin ?? undefined,
            notes: props.contact.notes ?? undefined,
          }
        : null;
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
async function onFormSubmit(data: ContactCreate) {
  if (props.contact && !isAdmin.value) {
    showError(t("common.unauthorized"));
    return;
  }
  saving.value = true;
  try {
    data.companyId = props.companyId;
    if (props.contact) {
      await contactStore.updateContact(props.contact.id, data);
      showSuccess(t("notifications.contactUpdated"));
    } else {
      await contactStore.createContact(props.companyId, data);
      showSuccess(t("notifications.contactCreated"));
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
  <AppModal :open="open" :title="title()" max-width="lg" @close="onClose">
    <ContactForm
      :initial-data="initialData"
      :disabled="saving || (!!props.contact && !isAdmin)"
      @submit="onFormSubmit"
      @cancel="onClose"
    />
  </AppModal>
</template>