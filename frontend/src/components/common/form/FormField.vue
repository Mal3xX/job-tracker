<script setup lang="ts">
/**
 * Componente wrapper per campi form con label, helper text e gestione errori
 */
interface Props {
  label?: string;
  error?: string;
  helperText?: string;
  required?: boolean;
}

withDefaults(defineProps<Props>(), {
  required: false,
});
</script>

<template>
  <div class="flex flex-col gap-1">
    <div v-if="label" class="flex items-center gap-1 mb-1">
      <label class="text-sm font-medium text-gray-700">{{ label }}</label>
      <span v-if="required" class="text-red-500">*</span>
    </div>
    <slot />
    <p v-if="helperText && !error" class="text-sm text-gray-500">
      {{ helperText }}
    </p>
    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>
