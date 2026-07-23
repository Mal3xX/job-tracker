<script setup lang="ts">
/**
 * Componente per raggruppare campi form in sezioni con titolo opzionale
 */
interface Props {
  title?: string;
  description?: string;
}

defineProps<Props>();
</script>

<template>
  <div class="space-y-4">
    <div v-if="title" class="border-b pb-3">
      <h3 class="text-base font-semibold text-gray-900">{{ title }}</h3>
      <p v-if="description" class="mt-1 text-sm text-gray-500">
        {{ description }}
      </p>
    </div>
    <div class="space-y-4">
      <slot />
    </div>
  </div>
</template>
