<script setup lang="ts">
/**
 * Combobox per la selezione della piattaforma.
 * Mostra le piattaforme già usate come suggerimenti, permette digitazione libera
 * per crearne di nuove. Filtraggio istantaneo a ogni keystroke.
 */
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useApplicationStore } from "@/stores/useApplicationStore";

const appStore = useApplicationStore();

const props = defineProps<{
  modelValue: string;
  placeholder?: string;
}>();

const emit = defineEmits<{
  "update:modelValue": [value: string];
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);
const showDropdown = ref(false);
const highlightIndex = ref(-1);
const query = ref("");

/** Piattaforme filtrate in base alla query */
const filteredPlatforms = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (!q) return appStore.platformNames.map((p) => ({ name: p, isNew: false }));
  const matches = appStore.platformNames
    .filter((p) => p.toLowerCase().includes(q))
    .map((p) => ({ name: p, isNew: false }));
  const exactMatch = appStore.platformNames.some((p) => p.toLowerCase() === q);
  if (!exactMatch && q) {
    matches.push({ name: q, isNew: true });
  }
  return matches;
});

/** Gestisce la digitazione nell'input */
function onInput(event: Event) {
  const target = event.target as HTMLInputElement;
  query.value = target.value;
  emit("update:modelValue", target.value);
  showDropdown.value = true;
  highlightIndex.value = -1;
}

/** Seleziona una piattaforma dalla lista */
function select(platform: { name: string; isNew: boolean }) {
  query.value = platform.name;
  emit("update:modelValue", platform.name);

  if (platform.isNew && !appStore.platformNames.includes(platform.name)) {
    const updated = [...appStore.platformNames, platform.name].sort();
    appStore.platformNames = updated;
  }

  showDropdown.value = false;
  highlightIndex.value = -1;
  inputRef.value?.focus();
}

/** Navigazione da tastiera nel dropdown */
function onKeydown(event: KeyboardEvent) {
  if (!showDropdown.value) return;
  if (event.key === "ArrowDown") {
    event.preventDefault();
    highlightIndex.value = Math.min(
      highlightIndex.value + 1,
      filteredPlatforms.value.length - 1,
    );
  } else if (event.key === "ArrowUp") {
    event.preventDefault();
    highlightIndex.value = Math.max(highlightIndex.value - 1, 0);
  } else if (event.key === "Enter") {
    event.preventDefault();
    if (highlightIndex.value >= 0) {
      select(filteredPlatforms.value[highlightIndex.value]);
    } else {
      showDropdown.value = false;
    }
  } else if (event.key === "Escape") {
    showDropdown.value = false;
    highlightIndex.value = -1;
  }
}

function onFocus() {
  showDropdown.value = true;
  highlightIndex.value = -1;
}

/** Chiude il dropdown al click fuori */
function onClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement;
  if (dropdownRef.value && !dropdownRef.value.contains(target)) {
    showDropdown.value = false;
  }
}

onMounted(() => {
  appStore.fetchPlatformNames();
  if (props.modelValue) {
    query.value = props.modelValue;
  }
  document.addEventListener("click", onClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", onClickOutside);
});
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <input
      ref="inputRef"
      :value="query"
      type="text"
      :placeholder="placeholder"
      class="input"
      @input="onInput"
      @keydown="onKeydown"
      @focus="onFocus"
      autocomplete="off"
    />
    <ul
      v-if="showDropdown && filteredPlatforms.length > 0"
      class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
    >
      <li
        v-for="(platform, index) in filteredPlatforms"
        :key="platform.isNew ? '__new__' : platform.name"
        class="px-3 py-2 cursor-pointer text-sm transition-colors"
        :class="
          index === highlightIndex
            ? 'bg-primary-100 text-primary-700'
            : 'hover:bg-gray-100 text-gray-700'
        "
        @mousedown.prevent="select(platform)"
        @mouseenter="highlightIndex = index"
      >
        <template v-if="platform.isNew">
          <span class="text-primary-600 font-medium">
            Aggiungi "{{ platform.name }}"
          </span>
        </template>
        <template v-else>
          {{ platform.name }}
        </template>
      </li>
    </ul>
  </div>
</template>
