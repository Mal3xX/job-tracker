<script setup lang="ts">
/**
 * Componente badge per mostrare etichette colorate
 */
type BadgeVariant = "gray" | "blue" | "green" | "red" | "yellow" | "purple";

interface Props {
  variant?: BadgeVariant;
  size?: "sm" | "md";
}

withDefaults(defineProps<Props>(), {
  variant: "gray",
  size: "md",
});
</script>

<template>
  <span
    class="inline-flex items-center font-medium rounded-full"
    :class="[
      size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-sm',
      variant === 'gray' && 'bg-gray-100 text-gray-700',
      variant === 'blue' && 'bg-blue-100 text-blue-700',
      variant === 'green' && 'bg-green-100 text-green-700',
      variant === 'red' && 'bg-red-100 text-red-700',
      variant === 'yellow' && 'bg-yellow-100 text-yellow-700',
      variant === 'purple' && 'bg-purple-100 text-purple-700',
    ]"
  >
    <slot />
  </span>
</template>
