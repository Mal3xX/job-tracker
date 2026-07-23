<script setup lang="ts">
/**
 * Pagina di login con form email/password
 */
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import AuthLayout from "@/views/layouts/AuthLayout.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppButton from "@/components/common/AppButton.vue";

const { t } = useI18n();
const { showError } = useToast();
const router = useRouter();
const authStore = useAuthStore();
const email = ref("");
const password = ref("");

async function handleSubmit() {
  try {
    await authStore.login({ email: email.value, password: password.value });
    router.push({ name: "dashboard" });
  } catch (err: any) {
    showError(t("auth.loginError"));
  }
}
</script>

<template>
  <AuthLayout>
    <form class="space-y-4" @submit.prevent="handleSubmit">
      <h2 class="text-xl font-semibold text-gray-900 text-center">
        {{ t("auth.loginTitle") }}
      </h2>
      <AppInput
        v-model="email"
        :label="t('auth.email')"
        type="email"
        :placeholder="t('auth.emailPlaceholder')"
      />
      <AppInput
        v-model="password"
        :label="t('auth.password')"
        type="password"
        :placeholder="t('auth.passwordPlaceholder')"
      />
      <AppButton type="submit" class="w-full" :loading="authStore.loading">
        {{ t("auth.login") }}
      </AppButton>
      <p class="text-sm text-center text-gray-500">
        {{ t("auth.noAccount") }}
        <router-link
          :to="{ name: 'register' }"
          class="text-primary-600 hover:underline"
        >
          {{ t("auth.register") }}
        </router-link>
      </p>
    </form>
  </AuthLayout>
</template>
