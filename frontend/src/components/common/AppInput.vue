<script setup lang="ts">
/**
 * Componente input riutilizzabile con label, errori e icona
 */
interface Props {
  modelValue: string;
  label?: string;
  placeholder?: string;
  error?: string;
  type?: "text" | "email" | "password" | "number" | "url" | "tel" | "date";
  disabled?: boolean;
  readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: "text",
  disabled: false,
  readonly: false,
});

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

function onInput(event: Event) {
  const target = event.target as HTMLInputElement;
  emit("update:modelValue", target.value);
}
</script>

<template>
  <div class="flex flex-col gap-1">
    <label v-if="label" class="text-sm font-medium text-gray-700">
      {{ label }}
    </label>
    <input
      :value="modelValue"
      :type="type"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      class="input"
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
