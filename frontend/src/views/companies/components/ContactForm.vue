<script setup lang="ts">
/**
 * Form puro per creazione/modifica contatto aziendale
 * Utilizzato da ContactFormModal e da eventuali viste standalone
 */
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppTextarea from "@/components/common/AppTextarea.vue";
import FormField from "@/components/common/form/FormField.vue";
import type { ContactCreate } from "@/types/contact.types";

const { t } = useI18n();

interface Props {
  initialData?: ContactCreate | null;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  initialData: null,
  disabled: false,
});

const emit = defineEmits<{
  submit: [data: ContactCreate];
  cancel: [];
}>();

const form = ref({
  name: "",
  email: "",
  phone: "",
  role: "",
  linkedin: "",
  notes: "",
});

const fieldErrors = ref<Partial<Record<keyof ContactCreate, string>>>({});

/**
 * Quando initialData cambia (es. apertura modale in edit), aggiorna il form
 */
watch(
  () => props.initialData,
  (val) => {
    if (val) {
      form.value = {
        name: val.name ?? "",
        email: val.email ?? "",
        phone: val.phone ?? "",
        role: val.role ?? "",
        linkedin: val.linkedin ?? "",
        notes: val.notes ?? "",
      };
      fieldErrors.value = {};
    }
  },
  { immediate: true },
);

/**
 * Valida i campi e, se ok, emette i dati per il submit
 */
function onSubmit() {
  fieldErrors.value = {};
  if (!form.value.name.trim()) {
    fieldErrors.value.name = t("form.required", {
      field: t("contacts.name"),
    });
    return;
  }
  const data: ContactCreate = {
    companyId: 0, // placeholder, viene sovrascritto dal wrapper
    name: form.value.name.trim(),
    email: form.value.email || undefined,
    phone: form.value.phone || undefined,
    role: form.value.role || undefined,
    linkedin: form.value.linkedin || undefined,
    notes: form.value.notes || undefined,
  };
  emit("submit", data);
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="onSubmit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <FormField :label="t('contacts.name')" required :error="fieldErrors.name">
        <AppInput v-model="form.name" :disabled="disabled" :placeholder="t('contacts.name')" />
      </FormField>
      <FormField :label="t('contacts.role')">
        <AppInput v-model="form.role" :disabled="disabled" :placeholder="t('contacts.role')" />
      </FormField>
      <FormField :label="t('contacts.email')">
        <AppInput
          v-model="form.email"
          type="email"
          :disabled="disabled"
          :placeholder="t('contacts.email')"
        />
      </FormField>
      <FormField :label="t('contacts.phone')">
        <AppInput
          v-model="form.phone"
          type="tel"
          :disabled="disabled"
          :placeholder="t('contacts.phone')"
        />
      </FormField>
      <div class="md:col-span-2">
        <FormField :label="t('contacts.linkedin')">
          <AppInput
            v-model="form.linkedin"
            type="url"
            :disabled="disabled"
            placeholder="https://linkedin.com/in/..."
          />
        </FormField>
      </div>
    </div>
    <FormField :label="t('contacts.notes')">
      <AppTextarea
        v-model="form.notes"
        :disabled="disabled"
        :placeholder="t('contacts.notes')"
        :rows="3"
      />
    </FormField>
    <div class="flex items-center justify-end gap-3 pt-4 border-t">
      <AppButton type="button" variant="secondary" @click="emit('cancel')">
        {{ t("common.cancel") }}
      </AppButton>
      <AppButton type="submit" :disabled="disabled">
        {{ initialData ? t("common.saveChanges") : t("common.create") }}
      </AppButton>
    </div>
  </form>
</template>
