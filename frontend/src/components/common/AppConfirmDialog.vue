<script setup lang="ts">
/**
 * Finestra modale di conferma globale (sostituisce window.confirm)
 */
import { useI18n } from "vue-i18n";
import { useConfirm } from "@/composables/useConfirm";
import AppModal from "@/components/common/AppModal.vue";
import AppButton from "@/components/common/AppButton.vue";

const { t } = useI18n();
const { visible, options, onConfirm, onCancel } = useConfirm();

/**
 * Icone Heroicons in base al type
 */
const icons: Record<string, string> = {
  danger:
    "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z",
  warning: "M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
  info: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
};

const iconColors: Record<string, string> = {
  danger: "bg-red-100 text-red-600",
  warning: "bg-amber-100 text-amber-600",
  info: "bg-blue-100 text-blue-600",
};
</script>

<template>
  <AppModal
    :open="visible"
    :title="options.title"
    max-width="sm"
    hide-dividers
    @close="onCancel"
  >
    <div class="flex items-start gap-4">
      <div
        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
        :class="iconColors[options.type || 'info']"
      >
        <svg
          class="w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            :d="icons[options.type || 'info']"
          />
        </svg>
      </div>
      <p class="text-sm text-gray-600">{{ options.message }}</p>
    </div>
    <template #footer>
      <AppButton variant="secondary" @click="onCancel">
        {{ options.cancelText || t("common.cancel") }}
      </AppButton>
      <AppButton
        :variant="options.type === 'danger' ? 'danger' : 'primary'"
        @click="onConfirm"
      >
        {{ options.confirmText || t("common.confirm") }}
      </AppButton>
    </template>
  </AppModal>
</template>
