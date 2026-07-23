import { ref } from "vue";

export interface ConfirmOptions {
  title?: string;
  message: string;
  confirmText?: string;
  cancelText?: string;
  type?: "danger" | "warning" | "info";
}

const visible = ref(false);
const options = ref<ConfirmOptions>({ message: "" });
let resolvePromise: ((value: boolean) => void) | null = null;

/**
 * Composable singleton per finestre di conferma (sostituisce window.confirm)
 */
export function useConfirm() {
  /**
   * Mostra il dialog di conferma e restituisce una Promise
   * @returns Promise<boolean> - true se confermato, false se annullato
   */
  function confirm(opts: ConfirmOptions): Promise<boolean> {
    options.value = { ...opts };
    visible.value = true;
    return new Promise((resolve) => {
      resolvePromise = resolve;
    });
  }

  /** Conferma l'azione e chiude il dialog */
  function onConfirm() {
    visible.value = false;
    resolvePromise?.(true);
    resolvePromise = null;
  }

  /** Annulla l'azione e chiude il dialog */
  function onCancel() {
    visible.value = false;
    resolvePromise?.(false);
    resolvePromise = null;
  }
  
  return {
    visible,
    options,
    confirm,
    onConfirm,
    onCancel,
  };
}
