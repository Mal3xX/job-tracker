<script setup lang="ts">
/**
 * Pagina di registrazione con form nome/cognome/email/password/confirm
 */
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import AuthLayout from "@/views/layouts/AuthLayout.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppButton from "@/components/common/AppButton.vue";

const router = useRouter();
const authStore = useAuthStore();
const { t } = useI18n();
const { showError } = useToast();
const firstName = ref("");
const lastName = ref("");
const email = ref("");
const password = ref("");
const passwordConfirmation = ref("");

async function handleSubmit() {
  if (password.value !== passwordConfirmation.value) {
    showError(t("form.passwordMismatch"));
    return;
  }
  try {
    await authStore.register({
      first_name: firstName.value,
      last_name: lastName.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    router.push({ name: "dashboard" });
  } catch (err: any) {
    showError(t("errors.auth.register"));
  }
}
</script>

<template>
  <AuthLayout>
    <form class="space-y-4" @submit.prevent="handleSubmit">
      <h2 class="text-xl font-semibold text-gray-900 text-center">
        {{ t("auth.registerTitle") }}
      </h2>
      <div class="grid grid-cols-2 gap-4">
        <AppInput
          v-model="firstName"
          :label="t('auth.name')"
          type="text"
          :placeholder="t('auth.name')"
        />
        <AppInput
          v-model="lastName"
          :label="t('auth.lastName')"
          type="text"
          :placeholder="t('auth.lastName')"
        />
      </div>
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
      <AppInput
        v-model="passwordConfirmation"
        :label="t('auth.passwordConfirm')"
        type="password"
        :placeholder="t('auth.passwordPlaceholder')"
      />
      <AppButton type="submit" class="w-full" :loading="authStore.loading">
        {{ t("auth.register") }}
      </AppButton>
      <p class="text-sm text-center text-gray-500">
        {{ t("auth.hasAccount") }}
        <router-link
          :to="{ name: 'login' }"
          class="text-primary-600 hover:underline"
        >
          {{ t("auth.login") }}
        </router-link>
      </p>
    </form>
  </AuthLayout>
</template>
