<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

interface Column {
  key: string;
  label: string;
  sortable?: boolean;
  width?: string;
}

interface Props {
  columns: Column[];
  data: Record<string, any>[];
  loading?: boolean;
  sortKey?: string;
  sortOrder?: "asc" | "desc";
}

withDefaults(defineProps<Props>(), {
  loading: false,
});

const emit = defineEmits<{
  "update:sortKey": [value: string];
  "update:sortOrder": [value: "asc" | "desc"];
  sort: [key: string];
}>();

function onSort(key: string) {
  emit("sort", key);
}
</script>

<template>
  <div class="overflow-x-auto rounded-lg border">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :style="col.width ? { width: col.width } : undefined"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            :class="[
              col.sortable && 'cursor-pointer hover:bg-gray-100 select-none',
            ]"
            @click="col.sortable && onSort(col.key)"
          >
            <div class="flex items-center gap-1">
              {{ col.label }}
              <span
                v-if="col.sortable && sortKey === col.key"
                class="text-gray-400"
              >
                {{ sortOrder === "asc" ? "↑" : "↓" }}
              </span>
            </div>
          </th>
          <th
            v-if="$slots.actions"
            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            {{ t('common.actions') }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-if="loading">
          <td
            :colspan="columns.length + ($slots.actions ? 1 : 0)"
            class="text-center py-8 text-gray-500"
          >
            {{ t('common.loading') }}
          </td>
        </tr>
        <tr v-else-if="data.length === 0">
          <td
            :colspan="columns.length + ($slots.actions ? 1 : 0)"
            class="text-center py-8 text-gray-500"
          >
            {{ t('common.noData') }}
          </td>
        </tr>
        <tr
          v-for="(row, rowIndex) in data"
          :key="rowIndex"
          class="hover:bg-gray-50"
        >
          <td
            v-for="col in columns"
            :key="col.key"
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ row[col.key] }}
            </slot>
          </td>
          <td
            v-if="$slots.actions"
            class="px-6 py-4 whitespace-nowrap text-sm text-right"
          >
            <slot name="actions" :row="row" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
