<script setup lang="ts">
/**
 * Componente loader/spinner per stati di caricamento
 */
interface Props {
  size?: "sm" | "md" | "lg";
  text?: string;
}

withDefaults(defineProps<Props>(), {
  size: "md",
});
</script>

<template>
  <div class="flex flex-col items-center justify-center gap-3 py-8">
    <svg
      class="animate-spin text-primary-600"
      :class="[
        size === 'sm' && 'h-5 w-5',
        size === 'md' && 'h-8 w-8',
        size === 'lg' && 'h-12 w-12',
      ]"
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
    <p v-if="text" class="text-sm text-gray-500">{{ text }}</p>
  </div>
</template>
