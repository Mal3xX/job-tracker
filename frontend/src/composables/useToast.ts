import { ref } from "vue";

export interface Toast {
  id: number;
  message: string;
  type: "success" | "error" | "warning" | "info";
  duration: number
}

export interface ToastOptions {
  duration?: number;
}

let nextId = 0;
const toasts = ref<Toast[]>([]);

/**
 * Aggiunge un toast con messaggio e tipo specifico
 * Il toast viene rimosso automaticamente dopo 4 secondi
 */
function addToast(message: string, type: Toast["type"] = "info", options?: ToastOptions) {
  const id = nextId++;
  const duration = options?.duration ?? 4000;
  toasts.value.push({ id, message, type, duration });
  setTimeout(() => removeToast(id), duration);
}

/**
 * Rimuove un toast dato il suo ID
 */
function removeToast(id: number) {
  toasts.value = toasts.value.filter((t) => t.id !== id);
}

/**
 * Mostra un toast di tipo success
 */
function showSuccess(message: string, options?: ToastOptions) {
  addToast(message, "success", options);
}

/**
 * Mostra un toast di tipo error
 */
function showError(message: string, options?: ToastOptions) {
  addToast(message, "error", options);
}

/**
 * Mostra un toast di tipo warning
 */
function showWarning(message: string, options?: ToastOptions) {
  addToast(message, "warning", options);
}

/**
 * Mostra un toast di tipo info
 */
function showInfo(message: string, options?: ToastOptions) {
  addToast(message, "info", options);
}

/**
 * Composable per gestire notifiche toast
 * Fornisce funzioni per mostrare e rimuovere toast temporanei
 */
export function useToast() {
  return {
    toasts,
    addToast,
    removeToast,
    showSuccess,
    showError,
    showWarning,
    showInfo,
  };
}
