<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
import { useCompanyStore } from "@/stores/useCompanyStore";

const { t } = useI18n();
const companyStore = useCompanyStore();
const props = defineProps<{
  modelValue: string;
  placeholder?: string;
}>();

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

const input = ref<HTMLInputElement | null>(null);
const suggestions = ref<{ id: number; name: string }[]>([]);
const showDropdown = ref(false);
const highlightIndex = ref(-1);
const blurTimeout = ref<ReturnType<typeof setTimeout> | null>(null);

onMounted(() => {
  companyStore.fetchCompanyNames();
});

function similarity(a: string, b: string): number {
  const aLower = a.toLowerCase();
  const bLower = b.toLowerCase();
  if (aLower === bLower) return 100;
  if (aLower.startsWith(bLower)) return 80;
  if (aLower.includes(bLower)) return 60;
  return 0;
}

function filter(query: string) {
  if (!query.trim()) {
    suggestions.value = [];
    showDropdown.value = false;
    return;
  }
  const q = query.trim().toLowerCase();
  const scored = companyStore.companyNames
    .map((c) => ({ ...c, score: similarity(c.name, q) }))
    .filter((c) => c.score > 0)
    .sort((a, b) => b.score - a.score || a.name.localeCompare(b.name))
    .slice(0, 8);
  suggestions.value = scored;
  showDropdown.value = scored.length > 0;
  highlightIndex.value = -1;
}

function select(suggestion: { id: number; name: string }) {
  emit("update:modelValue", suggestion.name);
  showDropdown.value = false;
  highlightIndex.value = -1;
}

function onInput(event: Event) {
  const target = event.target as HTMLInputElement;
  emit("update:modelValue", target.value);
  filter(target.value);
}

function onKeydown(event: KeyboardEvent) {
  if (!showDropdown.value) return;
  if (event.key === "ArrowDown") {
    event.preventDefault();
    highlightIndex.value = Math.min(
      highlightIndex.value + 1,
      suggestions.value.length - 1,
    );
  } else if (event.key === "ArrowUp") {
    event.preventDefault();
    highlightIndex.value = Math.max(highlightIndex.value - 1, 0);
  } else if (event.key === "Enter") {
    event.preventDefault();
    if (highlightIndex.value >= 0) {
      select(suggestions.value[highlightIndex.value]);
    }
  } else if (event.key === "Escape") {
    showDropdown.value = false;
    highlightIndex.value = -1;
    input.value?.blur();
  }
}

function onFocus() {
  if (props.modelValue) filter(props.modelValue);
}

function onBlur() {
  blurTimeout.value = setTimeout(() => {
    showDropdown.value = false;
    highlightIndex.value = -1;
  }, 150);
}

onUnmounted(() => {
  if (blurTimeout.value) clearTimeout(blurTimeout.value);
});
</script>

<template>
  <div class="relative">
    <input
      ref="input"
      :value="modelValue"
      type="text"
      :placeholder="placeholder"
      class="input"
      @input="onInput"
      @keydown="onKeydown"
      @focus="onFocus"
      @blur="onBlur"
      autocomplete="off"
    />
    <ul
      v-if="showDropdown && suggestions.length > 0"
      class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
    >
      <li
        v-for="(suggestion, index) in suggestions"
        :key="suggestion.id"
        class="px-3 py-2 cursor-pointer text-sm transition-colors"
        :class="
          index === highlightIndex
            ? 'bg-primary-100 text-primary-700'
            : 'hover:bg-gray-100 text-gray-700'
        "
        @mousedown.prevent="select(suggestion)"
        @mouseenter="highlightIndex = index"
      >
        {{ suggestion.name }}
      </li>
    </ul>
    <div
      v-if="modelValue && showDropdown && suggestions.length === 0"
      class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg px-3 py-2 text-sm text-gray-400"
    >
      {{ t("companies.autocomplete.noResults") }}
    </div>
  </div>
</template>
