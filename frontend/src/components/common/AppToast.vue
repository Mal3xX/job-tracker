<script setup lang="ts">
/**
 * Componente per visualizzare notifiche toast temporanee
 * Importa direttamente lo stato singleton da useToast
 */
import { useToast } from "@/composables/useToast";

const { toasts, removeToast } = useToast();

const iconMap: Record<string, string> = {
  success: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
  error: "M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z",
  warning:
    "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z",
  info: "M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z",
};

const bgMap: Record<string, string> = {
  success: "bg-green-50 border-green-200 text-green-800",
  error: "bg-red-50 border-red-200 text-red-800",
  warning: "bg-yellow-50 border-yellow-200 text-yellow-800",
  info: "bg-blue-50 border-blue-200 text-blue-800",
};

const iconBgMap: Record<string, string> = {
  success: "text-green-500",
  error: "text-red-500",
  warning: "text-yellow-500",
  info: "text-blue-500",
};

const progressBgMap: Record<string, string> = {
  success: "bg-green-400",
  error: "bg-red-400",
  warning: "bg-yellow-400",
  info: "bg-blue-400",
};
</script>

<template>
  <div class="fixed top-4 right-4 z-60 flex flex-col gap-2 max-w-sm pointer-events-none">
    <TransitionGroup
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-300 ease-in"
      enter-from-class="opacity-0 translate-x-4"
      leave-to-class="opacity-0 translate-x-4"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="flex items-start gap-3 px-4 py-3 rounded-lg border shadow-xl overflow-hidden pointer-events-auto"
        :class="bgMap[toast.type]"
      >
        <svg
          class="w-5 h-5 mt-0.5 flex-shrink-0"
          :class="iconBgMap[toast.type]"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            :d="iconMap[toast.type]"
          />
        </svg>
        <p class="text-sm flex-1">{{ toast.message }}</p>
        <button
          class="text-current opacity-50 hover:opacity-100 flex-shrink-0"
          @click="removeToast(toast.id)"
        >
          <svg
            class="w-4 h-4"
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
        <div
          class="absolute bottom-0 left-0 h-1 rounded-full"
          :class="progressBgMap[toast.type]"
        />
      </div>
    </TransitionGroup>
  </div>
</template>