<script setup lang="ts">
/**
 * Componente pulsante riutilizzabile con varianti e dimensioni
 */
type ButtonVariant = "primary" | "secondary" | "danger" | "outline";
type ButtonSize = "sm" | "md" | "lg";

interface Props {
  variant?: ButtonVariant;
  size?: ButtonSize;
  disabled?: boolean;
  loading?: boolean;
  type?: "button" | "submit" | "reset";
}

const props = withDefaults(defineProps<Props>(), {
  variant: "primary",
  size: "md",
  disabled: false,
  loading: false,
  type: "button",
});

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();
</script>

<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    class="btn inline-flex items-center justify-center gap-2 font-medium transition-colors duration-200 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
    :class="[
      variant === 'primary' && 'bg-primary-600 text-white hover:bg-primary-700',
      variant === 'secondary' && 'bg-gray-200 text-gray-700 hover:bg-gray-300',
      variant === 'danger' && 'bg-red-600 text-white hover:bg-red-700',
      variant === 'outline' &&
        'border border-gray-300 text-gray-700 hover:bg-gray-50',
      size === 'sm' && 'px-3 py-1.5 text-sm',
      size === 'md' && 'px-4 py-2 text-sm',
      size === 'lg' && 'px-6 py-3 text-base',
    ]"
    @click="emit('click', $event)"
  >
    <svg
      v-if="loading"
      class="animate-spin h-4 w-4"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
      />
    </svg>
    <slot />
  </button>
</template>
