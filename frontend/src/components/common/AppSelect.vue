<script setup lang="ts">
/**
 * Componente select riutilizzabile con label e gestione errori
 */
interface SelectOption {
  value: string | number;
  label: string;
}

interface Props {
  modelValue: string | number;
  label?: string;
  options: SelectOption[];
  placeholder?: string;
  error?: string;
  disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
});

const emit = defineEmits<{
  "update:modelValue": [value: string | number];
}>();

function onChange(event: Event) {
  const target = event.target as HTMLSelectElement;
  emit("update:modelValue", target.value);
}
</script>

<template>
  <div class="flex flex-col gap-1">
    <label v-if="label" class="text-sm font-medium text-gray-700">
      {{ label }}
    </label>
    <select
      :value="modelValue"
      :disabled="disabled"
      class="input appearance-none bg-white"
      :class="[
        error && 'border-red-500 focus:ring-red-500 focus:border-red-500',
      ]"
      @change="onChange"
    >
      <option v-if="placeholder" value="" disabled>
        {{ placeholder }}
      </option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">
        {{ opt.label }}
      </option>
    </select>
    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>
