<script setup lang="ts">
/**
 * Vista dettaglio azienda con info, contatti e storico candidature
 */
import { onMounted, computed, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useCompanyStore } from "@/stores/useCompanyStore";
import { useContactStore } from "@/stores/useContactStore";
import { useApplicationStore } from "@/stores/useApplicationStore";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import { useConfirm } from "@/composables/useConfirm";
import AppButton from "@/components/common/AppButton.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppCard from "@/components/common/AppCard.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import CompanyFormModal from "./components/CompanyFormModal.vue";
import ContactList from "./components/ContactList.vue";
import ContactFormModal from "./components/ContactFormModal.vue";
import { formatDate } from "@/utils/formatters";
import type { ApplicationStatus } from "@/types/application.types";
import type { Contact } from "@/types/contact.types";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const route = useRoute();
const router = useRouter();
const companyStore = useCompanyStore();
const contactStore = useContactStore();
const applicationStore = useApplicationStore();
const authStore = useAuthStore();
const companyId = Number(route.params.id);

function canManage(resource: { creatorId?: number }): boolean {
  return authStore.isAdmin || resource.creatorId === authStore.user?.id;
}
const showContactModal = ref(false);
const editingContact = ref<Contact | null>(null);
const showEditModal = ref(false);
const { confirm: confirmDialog } = useConfirm();

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
 * Filtra candidature nel store per questa azienda
 */
const companyApplications = computed(() =>
  applicationStore.applications.filter((a) => a.companyId === companyId),
);

/**
 * Label italiana per dimensione azienda
 */
function getSizeLabel(size: string | null): string {
  const map: Record<string, string> = {
    small: t("companies.sizes.small"),
    medium: t("companies.sizes.medium"),
    large: t("companies.sizes.large"),
  };
  return size ? map[size] || size : "-";
}

onMounted(async () => {
  await companyStore.fetchCompany(companyId);
  await contactStore.fetchContacts(companyId);
  await applicationStore.fetchApplications(1, companyId);
});

function onEdit() {
  showEditModal.value = true;
}

/**
 * Elimina l'azienda dopo conferma
 */
async function onDelete() {
  const confirmed = await confirmDialog({
    title: t("common.confirmDeleteTitle"),
    message: t("common.confirmDelete", { name: companyStore.currentCompany?.name }),
    type: "danger",
    confirmText: t("common.delete"),
    cancelText: t("common.cancel"),
  });
  if (!confirmed) return;
  try {
    await companyStore.deleteCompany(companyId);
    showSuccess(t("notifications.companyDeleted"));
    router.push({ name: "companies" });
  } catch (err: any) {
    if (err.status !== 403) {
      showError(err.message || t("common.unauthorized"));
    }
  }
}

/**
 * Elimina un contatto dopo conferma
 */
async function handleDeleteContact(contact: Contact) {
  const confirmed = await confirmDialog({
    title: t("common.confirmDeleteTitle"),
    message: t("common.confirmDelete", { name: contact.name }),
    type: "danger",
    confirmText: t("common.delete"),
    cancelText: t("common.cancel"),
  });
  if (!confirmed) return;
  try {
    await contactStore.deleteContact(contact.id);
    showSuccess(t("notifications.contactDeleted"));
  } catch (err: any) {
    if (err.status !== 403) {
      showError(err.message || t("common.unauthorized"));
    }
  }
}

/**
 * Torna alla lista aziende
 */
function onBack() {
  router.push({ name: "companies" });
}

/**
 * Ricarica l'azienda dopo una modifica salvata
 */
function onCompanySaved() {
  companyStore.fetchCompany(companyId);
}


/**
 * Apre la modale per creare un nuovo contatto
 */
function openAddContactModal() {
  editingContact.value = null;
  showContactModal.value = true;
}

/**
 * Apre la modale per modificare un contatto esistente
 */
function openEditContactModal(contact: Contact) {
  editingContact.value = contact;
  showContactModal.value = true;
}

/**
 * Ricarica i contatti dopo un salvataggio riuscito
 */
function onContactSaved() {
  contactStore.fetchContacts(companyId);
}
</script>

<template>
  <div class="space-y-6">
    <AppLoader v-if="companyStore.loading" :text="t('companies.loadingItem')" />
    <template v-else-if="companyStore.currentCompany">
      <!-- Header con nome e azioni -->
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
            {{ companyStore.currentCompany.name }}
          </h1>
        </div>
        <div v-if="canManage(companyStore.currentCompany)" class="flex items-center gap-2">
          <AppButton variant="secondary" @click="onEdit">{{
            t("common.edit")
          }}</AppButton>
          <AppButton variant="danger" @click="onDelete">{{
            t("common.delete")
          }}</AppButton>
        </div>
      </div>
      <!-- Info azienda -->
      <AppCard>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.sector") }}
            </h3>
            <p class="mt-1">{{ companyStore.currentCompany.sector || "-" }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.size") }}
            </h3>
            <p class="mt-1">
              {{ getSizeLabel(companyStore.currentCompany.size) }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.website") }}
            </h3>
            <p class="mt-1">
              <a
                v-if="companyStore.currentCompany.website"
                :href="companyStore.currentCompany.website"
                target="_blank"
                class="text-primary-600 hover:text-primary-800"
              >
                {{ companyStore.currentCompany.website }}
              </a>
              <span v-else class="text-gray-400">-</span>
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.linkedin") }}
            </h3>
            <p class="mt-1">
              <a
                v-if="companyStore.currentCompany.linkedin"
                :href="companyStore.currentCompany.linkedin"
                target="_blank"
                class="text-primary-600 hover:text-primary-800"
              >
                {{ companyStore.currentCompany.linkedin }}
              </a>
              <span v-else class="text-gray-400">-</span>
            </p>
          </div>
          <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.description") }}
            </h3>
            <p class="mt-1 text-gray-700 whitespace-pre-wrap">
              {{
                companyStore.currentCompany.description ||
                t("companies.fields.noDescription")
              }}
            </p>
          </div>
          <div class="md:col-span-2">
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.notes") }}
            </h3>
            <p class="mt-1 text-gray-700 whitespace-pre-wrap">
              {{
                companyStore.currentCompany.notes ||
                t("companies.fields.noNotes")
              }}
            </p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">
              {{ t("companies.fields.createdAt") }}
            </h3>
            <p class="mt-1">
              {{ formatDate(companyStore.currentCompany.createdAt) }}
            </p>
          </div>
        </div>
      </AppCard>
      <!-- Contatti -->
      <ContactList
        :contacts="contactStore.contacts"
        :loading="contactStore.loading"
        @add="openAddContactModal"
        @edit="openEditContactModal"
        @delete="handleDeleteContact"
      />
      <CompanyFormModal
        :open="showEditModal"
        :company-id="companyId"
        @close="showEditModal = false"
        @saved="onCompanySaved"
      />
      <ContactFormModal
        :open="showContactModal"
        :company-id="companyId"
        :contact="editingContact"
        @close="showContactModal = false"
        @saved="onContactSaved"
      />

      <!-- Storico candidature -->
      <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
          {{ t("companies.applicationHistory.title") }}
        </h2>
        <AppCard v-if="companyApplications.length === 0">
          <p class="text-gray-500 text-center py-4">
            {{ t("companies.applicationHistory.empty") }}
          </p>
        </AppCard>
        <div v-else class="space-y-3">
          <AppCard
            v-for="app in companyApplications"
            :key="app.id"
            :hover="true"
            @click="
              router.push({
                name: 'application-detail',
                params: { id: app.id },
              })
            "
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-medium text-gray-900">{{ app.title }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                  {{ formatDate(app.createdAt) }}
                </p>
              </div>
              <AppBadge :variant="statusVariants[app.status] as any">
                {{ t(`applications.statusLabels.${app.status}`) }}
              </AppBadge>
            </div>
          </AppCard>
        </div>
      </div>
    </template>
    <AppCard v-else>
      <p class="text-gray-500 text-center py-8">
        {{ t("companies.notFound") }}
      </p>
    </AppCard>
  </div>
</template>
