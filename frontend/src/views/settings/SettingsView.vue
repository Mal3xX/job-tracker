<script setup lang="ts">
/**
 * Pagina impostazioni profilo utente.
 * Permette di cambiare email, password e immagine profilo.
 */
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useAuthStore } from "@/stores/useAuthStore";
import { useToast } from "@/composables/useToast";
import FormSection from "@/components/common/form/FormSection.vue";
import FormField from "@/components/common/form/FormField.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppButton from "@/components/common/AppButton.vue";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const authStore = useAuthStore();
const saving = ref(false);
const uploadingAvatar = ref(false);

const form = ref({
  email: authStore.user?.email ?? "",
  password: "",
  passwordConfirmation: "",
});

const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const fieldErrors = ref<Record<string, string>>({});

/** URL dell'avatar corrente (se presente) */
const currentAvatarUrl = computed(() => {
  const path = authStore.user?.avatarPath;
  if (!path) return undefined;
  return `/storage/${path}`;
});

/** Anteprima: prima quella nuova, poi quella esistente */
const previewSrc = computed(
  () => avatarPreview.value ?? currentAvatarUrl.value,
);

/** Gestisce la selezione di un nuovo file avatar */
async function onAvatarSelected(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;
  avatarFile.value = file;
  avatarPreview.value = URL.createObjectURL(file);

  // Upload immediato
  uploadingAvatar.value = true;
  try {
    const formData = new FormData();
    formData.append("avatar", file);
    await authStore.updateProfile(formData);
    avatarFile.value = null;
    showSuccess(t("auth.profile.avatarUpdated"));
  } catch (err: any) {
    avatarPreview.value = null;
    avatarFile.value = null;
    if (fileInput.value) fileInput.value.value = "";
    if (err?.errors) {
      fieldErrors.value = {
        avatar:
          (err.errors as Record<string, string[]>).avatar?.[0] || err.message,
      };
    }
    showError(err?.message || t("errors.generic"));
  } finally {
    uploadingAvatar.value = false;
  }
}

/** Rimuove l'avatar selezionato e resetta l'input file */
async function removeAvatar() {
  uploadingAvatar.value = true;
  try {
    const formData = new FormData();
    formData.append("remove_avatar", "1");
    await authStore.updateProfile(formData);
    avatarPreview.value = null;
    if (fileInput.value) fileInput.value.value = "";
    showSuccess(t("auth.profile.avatarRemoved"));
  } catch (err: any) {
    if (err?.errors) {
      fieldErrors.value = {
        avatar:
          (err.errors as Record<string, string[]>).avatar?.[0] || err.message,
      };
    }
    showError(err?.message || t("errors.generic"));
  } finally {
    uploadingAvatar.value = false;
  }
}

async function onSubmit() {
  saving.value = true;
  fieldErrors.value = {};
  try {
    const formData = new FormData();
    if (form.value.email !== authStore.user?.email) {
      formData.append("email", form.value.email);
    }
    if (form.value.password) {
      formData.append("password", form.value.password);
      formData.append("password_confirmation", form.value.passwordConfirmation);
    }
    await authStore.updateProfile(formData);
    form.value.password = "";
    form.value.passwordConfirmation = "";
    avatarFile.value = null;
    avatarPreview.value = null;
    showSuccess(t("auth.profile.updated"));
  } catch (err: any) {
    if (err?.errors) {
      const mapped: Record<string, string> = {};
      for (const [key, messages] of Object.entries(err.errors)) {
        mapped[key] = (messages as string[])[0];
      }
      fieldErrors.value = mapped;
    }
    showError(err?.message || t("errors.generic"));
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">
      {{ t("auth.profile.title") }}
    </h1>
    <form class="space-y-6" @submit.prevent="onSubmit">
      <!-- Sezione Avatar -->
      <FormSection :title="t('auth.profile.avatar')">
        <div class="flex items-center gap-4">
          <div
            class="w-20 h-20 rounded-full bg-primary-600 text-white flex items-center justify-center text-2xl font-medium overflow-hidden shrink-0"
          >
            <img
              v-if="previewSrc"
              :src="previewSrc"
              alt="Avatar"
              class="w-full h-full object-cover"
            />
            <span v-else>
              {{ (authStore.user?.firstName ?? "").charAt(0).toUpperCase() }}
            </span>
          </div>
          <div class="flex flex-col gap-2">
            <label
              class="cursor-pointer"
              :class="{ 'opacity-50 pointer-events-none': uploadingAvatar }"
            >
              <span
                class="inline-block px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                {{ t("auth.profile.chooseImage") }}
              </span>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onAvatarSelected"
              />
            </label>
            <p v-if="uploadingAvatar" class="text-sm text-gray-500 mt-1">
              {{ t("common.saving") }}
            </p>
            <button
              v-if="avatarFile || currentAvatarUrl"
              type="button"
              class="text-sm text-red-600 hover:text-red-700 transition-colors"
              @click="removeAvatar"
              :disabled="uploadingAvatar"
            >
              {{ t("auth.profile.removeImage") }}
            </button>
          </div>
        </div>
        <p v-if="fieldErrors.avatar" class="text-sm text-red-600 mt-1">
          {{ fieldErrors.avatar }}
        </p>
      </FormSection>
      <!-- Sezione Email -->
      <FormSection :title="t('auth.profile.changeEmail')">
        <FormField :label="t('auth.email')" :error="fieldErrors.email">
          <AppInput
            v-model="form.email"
            type="email"
            :placeholder="t('auth.emailPlaceholder')"
          />
        </FormField>
      </FormSection>
      <!-- Sezione Password -->
      <FormSection :title="t('auth.profile.changePassword')">
        <p class="text-sm text-gray-500 mb-4">
          {{ t("auth.profile.leaveEmpty") }}
        </p>
        <div class="grid grid-cols-2 gap-4">
          <FormField
            :label="t('auth.profile.newPassword')"
            :error="fieldErrors.password"
          >
            <AppInput
              v-model="form.password"
              type="password"
              :placeholder="'••••••••'"
            />
          </FormField>
          <FormField
            :label="t('auth.profile.confirmPassword')"
            :error="fieldErrors.password_confirmation"
          >
            <AppInput
              v-model="form.passwordConfirmation"
              type="password"
              :placeholder="'••••••••'"
            />
          </FormField>
        </div>
      </FormSection>
      <!-- Submit -->
      <div class="flex justify-end pt-4 border-t">
        <AppButton type="submit" :disabled="saving">
          {{ saving ? t("common.saving") : t("common.saveChanges") }}
        </AppButton>
      </div>
    </form>
  </div>
</template>
