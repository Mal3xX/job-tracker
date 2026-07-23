<script setup lang="ts">
/**
 * Componente textarea riutilizzabile con label e gestione errori
 */
interface Props {
  modelValue: string;
  label?: string;
  placeholder?: string;
  error?: string;
  disabled?: boolean;
  rows?: number;
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  rows: 4,
});

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

function onInput(event: Event) {
  const target = event.target as HTMLTextAreaElement;
  emit("update:modelValue", target.value);
}
</script>

<template>
  <div class="flex flex-col gap-1">
    <label v-if="label" class="text-sm font-medium text-gray-700">
      {{ label }}
    </label>
    <textarea
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :rows="rows"
      class="input resize-vertical min-h-[80px]"
      :class="[
        error && 'border-red-500 focus:ring-red-500 focus:border-red-500',
      ]"
      @input="onInput"
    />
    <p v-if="error" class="text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>
