<script setup lang="ts">
/**
 * Layout per pagine di autenticazione (login/register)
 * Centra il contenuto verticalmente e orizzontalmente
 */
import { useI18n } from "vue-i18n";
import LanguageSwitcher from "@/components/common/LanguageSwitcher.vue";

const { t } = useI18n();
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md">
      <div class="flex items-start justify-between mb-8">
        <div class="text-center flex-1">
          <h1 class="text-2xl font-bold text-gray-900">
            {{ t("nav.appName") }}
          </h1>
          <p class="mt-2 text-sm text-gray-600">{{ t("nav.appTagline") }}</p>
        </div>
        <LanguageSwitcher class="mt-1" />
      </div>
      <div class="card p-8">
        <slot />
      </div>
    </div>
  </div>
</template>
