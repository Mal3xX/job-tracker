<script setup lang="ts">
/**
 * Componente modal/dialog con header, body, footer e chiusura su backdrop/escape
 */
interface Props {
  open: boolean;
  title?: string;
  maxWidth?: "sm" | "md" | "lg" | "xl" | "2xl";
  hideDividers?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  maxWidth: "md",
  hideDividers: false,
});

const emit = defineEmits<{
  "update:open": [value: boolean];
  close: [];
}>();

function onBackdropClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    emit("update:open", false);
    emit("close");
  }
}

function onEscapeKey() {
  emit("update:open", false);
  emit("close");
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-200"
      leave-active-class="transition-opacity duration-200"
      enter-from-class="opacity-0"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click="onBackdropClick"
        @keyup.escape="onEscapeKey"
      >
        <Transition
          enter-active-class="transition-transform duration-200"
          leave-active-class="transition-transform duration-200"
          enter-from-class="scale-95"
          leave-to-class="scale-95"
        >
          <div
            v-if="open"
            class="bg-white rounded-xl shadow-xl w-full overflow-y-auto max-h-[90vh]"
            :class="[
              maxWidth === 'sm' && 'max-w-sm',
              maxWidth === 'md' && 'max-w-md',
              maxWidth === 'lg' && 'max-w-lg',
              maxWidth === 'xl' && 'max-w-xl',
              maxWidth === '2xl' && 'max-w-2xl',
            ]"
          >
            <div
              v-if="title"
              class="flex items-center justify-between px-6 py-4"
              :class="{ 'border-b': !hideDividers }"
            >
              <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
              <button
                class="text-gray-400 hover:text-gray-600 transition-colors"
                @click="onEscapeKey"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
            <div class="px-6 py-4">
              <slot />
            </div>
            <div
              v-if="$slots.footer"
              class="flex justify-end gap-3 px-6 py-4 bg-gray-50"
              :class="{ 'border-t': !hideDividers }"
            >
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
