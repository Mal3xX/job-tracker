<script setup lang="ts">
/**
 * Form per creazione/modifica candidatura
 * Usato da ApplicationFormModal in modalità create ed edit
 */
import { ref, watch, computed } from "vue";
import { useI18n } from "vue-i18n";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppTextarea from "@/components/common/AppTextarea.vue";
import AppSelect from "@/components/common/AppSelect.vue";
import FormField from "@/components/common/form/FormField.vue";
import FormSection from "@/components/common/form/FormSection.vue";
import CompanyAutocomplete from "@/views/companies/components/CompanyAutocomplete.vue";
import PlatformAutocomplete from "@/views/applications/components/PlatformAutocomplete.vue";
import { APPLICATION_STATUS, WORK_MODE } from "@/utils/constants";
import type { ApplicationCreate } from "@/types/application.types";

const { t } = useI18n();

interface Props {
  initialData?: ApplicationCreate;
  submitting?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  submitting: false,
});

const emit = defineEmits<{
  submit: [data: ApplicationCreate];
  cancel: [];
}>();

const form = ref({
  title: "",
  companyName: "",
  status: "" as string,
  workMode: "" as string,
  location: "",
  platform: "",
  linkJob: "",
  salaryMin: "",
  salaryMax: "",
  interviewDate: "",
  description: "",
  notes: "",
});

const errors = ref<Partial<Record<keyof ApplicationCreate, string>>>({});

watch(
  () => props.initialData,
  (val) => {
    if (val) {
      form.value.title = val.title;
      form.value.companyName = val.companyName ?? "";
      form.value.status = val.status ?? "";
      form.value.workMode = val.workMode ?? "";
      form.value.location = val.location ?? "";
      form.value.platform = val.platform ?? "";
      form.value.linkJob = val.linkJob ?? "";
      form.value.salaryMin = val.salaryMin?.toString() ?? "";
      form.value.salaryMax = val.salaryMax?.toString() ?? "";
      form.value.interviewDate = val.interviewDate
        ? val.interviewDate.split("T")[0]
        : "";
      form.value.description = val.description ?? "";
      form.value.notes = val.notes ?? "";
    }
  },
  { immediate: true },
);

const statusOptions = computed(() => [
  {
    value: APPLICATION_STATUS.PENDING,
    label: t("applications.statusLabels.pending"),
  },
  {
    value: APPLICATION_STATUS.NEGATIVE,
    label: t("applications.statusLabels.negative"),
  },
  {
    value: APPLICATION_STATUS.POSITIVE,
    label: t("applications.statusLabels.positive"),
  },
  {
    value: APPLICATION_STATUS.INTERVIEW,
    label: t("applications.statusLabels.interview"),
  },
  {
    value: APPLICATION_STATUS.OFFER,
    label: t("applications.statusLabels.offer"),
  },
  {
    value: APPLICATION_STATUS.NO_RESPONSE,
    label: t("applications.statusLabels.no_response"),
  },
]);

const workModeOptions = computed(() => [
  { value: WORK_MODE.REMOTE, label: t("applications.workModeLabels.remote") },
  { value: WORK_MODE.OFFICE, label: t("applications.workModeLabels.office") },
  { value: WORK_MODE.HYBRID, label: t("applications.workModeLabels.hybrid") },
]);

function onSubmit() {
  errors.value = {};
  if (!form.value.title.trim()) {
    errors.value.title = t("form.required", {
      field: t("applications.fields.title"),
    });
    return;
  }
  const data: ApplicationCreate = {
    title: form.value.title,
    companyName: form.value.companyName || undefined,
    status: (form.value.status as ApplicationCreate["status"]) || undefined,
    workMode:
      (form.value.workMode as ApplicationCreate["workMode"]) || undefined,
    location: form.value.location || undefined,
    platform: form.value.platform || undefined,
    linkJob: form.value.linkJob || undefined,
    salaryMin: form.value.salaryMin ? Number(form.value.salaryMin) : undefined,
    salaryMax: form.value.salaryMax ? Number(form.value.salaryMax) : undefined,
    interviewDate: form.value.interviewDate || undefined,
    description: form.value.description || undefined,
    notes: form.value.notes || undefined,
  };
  emit("submit", data);
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="onSubmit">
    <FormSection :title="t('applications.sections.general')">
      <FormField :label="t('applications.fields.title')" :error="errors.title">
        <AppInput
          v-model="form.title"
          :placeholder="t('applications.fields.titlePlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.company')">
        <CompanyAutocomplete
          v-model="form.companyName"
          :placeholder="t('applications.fields.companyPlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.status')">
        <AppSelect
          v-model="form.status"
          :options="statusOptions"
          :placeholder="t('applications.fields.statusPlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.workMode')">
        <AppSelect
          v-model="form.workMode"
          :options="workModeOptions"
          :placeholder="t('applications.fields.workModePlaceholder')"
        />
      </FormField>
    </FormSection>
    <FormSection :title="t('applications.sections.details')">
      <FormField :label="t('applications.fields.location')">
        <AppInput
          v-model="form.location"
          :placeholder="t('applications.fields.locationPlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.platform')">
        <PlatformAutocomplete
          v-model="form.platform"
          :placeholder="t('applications.fields.platformPlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.link')">
        <AppInput
          v-model="form.linkJob"
          type="url"
          :placeholder="t('applications.fields.linkPlaceholder')"
        />
      </FormField>
      <div class="grid grid-cols-2 gap-4">
        <FormField :label="t('applications.fields.salaryMin')">
          <AppInput
            v-model="form.salaryMin"
            type="number"
            :placeholder="t('applications.fields.salaryMinPlaceholder')"
          />
        </FormField>
        <FormField :label="t('applications.fields.salaryMax')">
          <AppInput
            v-model="form.salaryMax"
            type="number"
            :placeholder="t('applications.fields.salaryMaxPlaceholder')"
          />
        </FormField>
      </div>
      <FormField :label="t('applications.fields.interviewDate')">
        <AppInput v-model="form.interviewDate" type="date" />
      </FormField>
    </FormSection>
    <FormSection :title="t('applications.sections.notes')">
      <FormField :label="t('applications.fields.description')">
        <AppTextarea
          v-model="form.description"
          :placeholder="t('applications.fields.descriptionPlaceholder')"
        />
      </FormField>
      <FormField :label="t('applications.fields.notes')">
        <AppTextarea
          v-model="form.notes"
          :placeholder="t('applications.fields.notesPlaceholder')"
        />
      </FormField>
    </FormSection>
    <div class="flex items-center justify-end gap-3 pt-4 border-t">
      <AppButton type="button" variant="secondary" @click="emit('cancel')">
        {{ t("common.cancel") }}
      </AppButton>
      <AppButton type="submit" :disabled="submitting">
        {{
          submitting
            ? t("common.saving")
            : initialData
              ? t("common.saveChanges")
              : t("applications.new")
        }}
      </AppButton>
    </div>
  </form>
</template>
