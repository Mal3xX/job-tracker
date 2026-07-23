<script setup lang="ts">
/**
 * Lista contatti per un'azienda con azioni di modifica/eliminazione
 */
import { useI18n } from "vue-i18n";
import AppButton from "@/components/common/AppButton.vue";
import AppCard from "@/components/common/AppCard.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import { useAuthStore } from "@/stores/useAuthStore";
import type { Contact } from "@/types/contact.types";

const { t } = useI18n();
const authStore = useAuthStore();

interface Props {
  contacts: Contact[];
  loading?: boolean;
}

withDefaults(defineProps<Props>(), {
  loading: false,
});

function canManage(resource: { creatorId?: number }): boolean {
  return authStore.isAdmin || resource.creatorId === authStore.user?.id;
}

const emit = defineEmits<{
  add: [];
  edit: [contact: Contact];
  delete: [contact: Contact];
}>();
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold text-gray-900">{{ t("companies.contacts.title") }}</h2>
      <AppButton size="sm" @click="emit('add')">{{ t("companies.contacts.add") }}</AppButton>
    </div>
    <AppLoader v-if="loading" :text="t('companies.contacts.loading')" />
    <AppCard v-else-if="contacts.length === 0">
      <p class="text-gray-500 text-center py-4">{{ t("companies.contacts.empty") }}</p>
    </AppCard>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <AppCard v-for="contact in contacts" :key="contact.id" :padding="false">
        <div class="p-4">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="font-medium text-gray-900">{{ contact.name }}</h3>
              <p v-if="contact.role" class="text-sm text-gray-500 mt-1">
                {{ contact.role }}
              </p>
              <p v-if="contact.email" class="text-sm text-primary-600 mt-2">
                {{ contact.email }}
              </p>
              <p v-if="contact.phone" class="text-sm text-gray-600 mt-1">
                {{ contact.phone }}
              </p>
            </div>
            <div v-if="canManage(contact)" class="flex items-center gap-1">
              <AppButton
                size="sm"
                variant="secondary"
                @click="emit('edit', contact)"
                >{{ t("common.edit") }}</AppButton
              >
              <AppButton
                size="sm"
                variant="danger"
                @click="emit('delete', contact)"
                >{{ t("common.delete") }}</AppButton
              >
            </div>
          </div>
        </div>
      </AppCard>
    </div>
  </div>
</template>
