<script setup lang="ts">
/**
 * Header con avatar cliccabile e menu utente (logout + impostazioni)
 */
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useAuthStore } from "@/stores/useAuthStore";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useToast } from "@/composables/useToast";
import LanguageSwitcher from "@/components/common/LanguageSwitcher.vue";

const authStore = useAuthStore();
const router = useRouter();
const { t } = useI18n();
const { showSuccess } = useToast();
const showMenu = ref(false);
const avatarContainer = ref<HTMLElement | null>(null);

/** URL completo dell'avatar a partire dal path relativo */
const avatarUrl = computed(() => {
  const path = authStore.user?.avatarPath;
  if (!path) return undefined;
  return `/storage/${path}`;
});

/** Chiude il menu al click fuori dal container */
function onClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement;
  if (avatarContainer.value && !avatarContainer.value.contains(target)) {
    showMenu.value = false;
  }
}

onMounted(() => {
  document.addEventListener("click", onClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", onClickOutside);
});

function goToSettings() {
  showMenu.value = false;
  router.push({ name: "settings" });
}

function handleLogout() {
  showMenu.value = false;
  authStore.logout();
  showSuccess(t("notifications.loggedOut"));
  router.push({ name: "login" });
}
</script>

<template>
  <header class="bg-white px-6 py-3 flex items-center justify-between gap-4">
    <div class="flex items-center gap-2">
      <LanguageSwitcher />
    </div>
    <div class="flex items-center gap-3">
      <p class="text-sm font-medium text-gray-900">
        {{ authStore.user?.firstName }} {{ authStore.user?.lastName }}
      </p>
      <div ref="avatarContainer" class="relative">
        <button
          class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-medium overflow-hidden focus:outline-none focus:ring-2 focus:ring-primary-400 transition-shadow"
          @click="showMenu = !showMenu"
        >
          <img
            v-if="avatarUrl"
            :src="avatarUrl"
            alt="Avatar"
            class="w-full h-full object-cover"
          />
          <span v-else>
            {{ (authStore.user?.firstName ?? "").charAt(0).toUpperCase() }}
          </span>
        </button>
        <div
          v-if="showMenu"
          class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
        >
          <button
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
            @mousedown.prevent="goToSettings"
          >
            {{ t("auth.settings") }}
          </button>
          <button
            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
            @mousedown.prevent="handleLogout"
          >
            {{ t("auth.logout") }}
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
