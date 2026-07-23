<script setup lang="ts">
/**
 * Form per creazione/modifica azienda
 * Usato da CompanyFormModal in modalità create ed edit
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppTextarea from "@/components/common/AppTextarea.vue";
import AppSelect from "@/components/common/AppSelect.vue";
import FormField from "@/components/common/form/FormField.vue";
import FormSection from "@/components/common/form/FormSection.vue";
import type { CompanyCreate } from "@/types/company.types";

const { t } = useI18n();

interface Props {
  initialData?: CompanyCreate;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  submit: [data: CompanyCreate];
  cancel: [];
}>();

const form = ref({
  name: "",
  sector: "",
  size: "" as "small" | "medium" | "large" | "",
  website: "",
  linkedin: "",
  description: "",
  notes: "",
});

watch(
  () => props.initialData,
  (val) => {
    if (val) {
      form.value.name = val.name;
      form.value.sector = val.sector ?? "";
      form.value.size = val.size ?? "";
      form.value.website = val.website ?? "";
      form.value.linkedin = val.linkedin ?? "";
      form.value.description = val.description ?? "";
      form.value.notes = val.notes ?? "";
    }
  },
  { immediate: true },
);

const errors = ref<Partial<Record<keyof CompanyCreate, string>>>({});

const sizeOptions = computed(() => [
  { value: "small", label: t("companies.sizes.small") },
  { value: "medium", label: t("companies.sizes.medium") },
  { value: "large", label: t("companies.sizes.large") },
]);

function onSubmit() {
  errors.value = {};
  if (!form.value.name.trim()) {
    errors.value.name = t("form.required", {
      field: t("companies.fields.name"),
    });
    return;
  }
  const data: CompanyCreate = {
    name: form.value.name,
    sector: form.value.sector || undefined,
    size: form.value.size || undefined,
    website: form.value.website || undefined,
    linkedin: form.value.linkedin || undefined,
    description: form.value.description || undefined,
    notes: form.value.notes || undefined,
  };
  emit("submit", data);
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="onSubmit">
    <FormSection :title="t('companies.sections.general')">
      <FormField :label="t('companies.fields.name')" :error="errors.name">
        <AppInput
          v-model="form.name"
          :disabled="disabled"
          :placeholder="t('companies.fields.namePlaceholder')"
        />
      </FormField>
      <FormField :label="t('companies.fields.sector')">
        <AppInput
          v-model="form.sector"
          :disabled="disabled"
          :placeholder="t('companies.fields.sectorPlaceholder')"
        />
      </FormField>
      <FormField :label="t('companies.fields.size')">
        <AppSelect
          v-model="form.size"
          :options="sizeOptions"
          :disabled="disabled"
          :placeholder="t('companies.fields.sizePlaceholder')"
        />
      </FormField>
    </FormSection>
    <FormSection :title="t('companies.sections.links')">
      <FormField :label="t('companies.fields.website')">
        <AppInput
          v-model="form.website"
          type="url"
          :disabled="disabled"
          :placeholder="t('companies.fields.websitePlaceholder')"
        />
      </FormField>
      <FormField :label="t('companies.fields.linkedin')">
        <AppInput
          v-model="form.linkedin"
          type="url"
          :disabled="disabled"
          :placeholder="t('companies.fields.linkedinPlaceholder')"
        />
      </FormField>
    </FormSection>
    <FormSection :title="t('companies.sections.notes')">
      <FormField :label="t('companies.fields.description')">
        <AppTextarea
          v-model="form.description"
          :disabled="disabled"
          :placeholder="t('companies.fields.descriptionPlaceholder')"
        />
      </FormField>
      <FormField :label="t('companies.fields.notes')">
        <AppTextarea
          v-model="form.notes"
          :disabled="disabled"
          :placeholder="t('companies.fields.notesPlaceholder')"
        />
      </FormField>
    </FormSection>
    <div class="flex items-center justify-end gap-3 pt-4 border-t">
      <AppButton type="button" variant="secondary" @click="emit('cancel')">
        {{ t("common.cancel") }}
      </AppButton>
      <AppButton type="submit" :disabled="disabled">
        {{ initialData ? t("common.saveChanges") : t("companies.new") }}
      </AppButton>
    </div>
  </form>
</template>
