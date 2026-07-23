<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

interface Props {
  currentPage: number;
  lastPage: number;
  total?: number;
}

const props = withDefaults(defineProps<Props>(), {
  total: 0,
});

const emit = defineEmits<{
  "page-change": [page: number];
}>();

function goToPage(page: number) {
  if (page >= 1 && page <= props.lastPage) {
    emit("page-change", page);
  }
}

function pagesToShow(): (number | string)[] {
  const pages: (number | string)[] = [];
  const delta = 2;
  for (let i = 1; i <= props.lastPage; i++) {
    if (
      i === 1 ||
      i === props.lastPage ||
      (i >= props.currentPage - delta && i <= props.currentPage + delta)
    ) {
      pages.push(i);
    } else if (pages[pages.length - 1] !== "...") {
      pages.push("...");
    }
  }
  return pages;
}
</script>

<template>
  <div v-if="lastPage > 1" class="flex items-center justify-between mt-4">
    <p v-if="total" class="text-sm text-gray-600">{{ t('common.total') }}: {{ total }}</p>
    <div class="flex items-center gap-1">
      <button
        :disabled="currentPage <= 1"
        class="px-3 py-1.5 text-sm rounded-lg border hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        @click="goToPage(currentPage - 1)"
      >
        {{ t('common.previous') }}
      </button>
      <button
        v-for="page in pagesToShow()"
        :key="page"
        :disabled="page === '...'"
        class="px-3 py-1.5 text-sm rounded-lg border min-w-[36px]"
        :class="[
          page === currentPage
            ? 'bg-primary-600 text-white border-primary-600'
            : page !== '...'
              ? 'hover:bg-gray-50'
              : 'border-transparent cursor-default',
        ]"
        @click="page !== '...' && goToPage(page as number)"
      >
        {{ page }}
      </button>
      <button
        :disabled="currentPage >= lastPage"
        class="px-3 py-1.5 text-sm rounded-lg border hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        @click="goToPage(currentPage + 1)"
      >
        {{ t('common.next') }}
      </button>
    </div>
  </div>
</template>
