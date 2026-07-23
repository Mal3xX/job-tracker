<script setup lang="ts">
/**
 * Componente card container con padding e ombra opzionale
 */
interface Props {
  padding?: boolean;
  hover?: boolean;
}

withDefaults(defineProps<Props>(), {
  padding: true,
  hover: false,
});
</script>

<template>
  <div
    class="card"
    :class="[
      padding && 'p-6',
      hover && 'hover:shadow-md transition-shadow duration-200 cursor-pointer',
    ]"
  >
    <slot />
  </div>
</template>
