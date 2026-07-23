<script setup lang="ts">
/**
 * Vista dettaglio utente admin con info, cambio ruolo e sessioni attive.
 */
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAdminStore } from "@/stores/useAdminStore";
import { useToast } from "@/composables/useToast";
import AppButton from "@/components/common/AppButton.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppCard from "@/components/common/AppCard.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import AppSelect from "@/components/common/AppSelect.vue";
import { formatDate } from "@/utils/formatters";

const { t } = useI18n();
const { showSuccess, showError } = useToast();
const route = useRoute();
const router = useRouter();
const adminStore = useAdminStore();
const userId = Number(route.params.id);

const roleOptions = computed(() => [
  { value: "admin", label: t("admin.roles.admin") },
  { value: "user", label: t("admin.roles.user") },
]);

const selectedRole = ref("");

onMounted(async () => {
  await adminStore.fetchUser(userId);
  selectedRole.value = adminStore.currentUser?.role || "";
});

/**
 * Salva il nuovo ruolo dell'utente.
 */
async function onSaveRole() {
  try {
    await adminStore.updateRole(userId, selectedRole.value);
    showSuccess(t("admin.detail.roleUpdated"));
  } catch (err: any) {
    if (err.status === 403) {
      showError(t("admin.detail.cannotChangeRole"));
    } else {
      showError(err.message || t("errors.generic"));
    }
  }
}

/**
 * Variante badge in base al ruolo.
 */
function getRoleVariant(role: string): "green" | "gray" {
  return role === "admin" ? "green" : "gray";
}

function onBack() {
  router.push({ name: "admin-users" });
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center gap-4">
      <button
        class="p-2 rounded-full hover:bg-gray-100 transition-colors"
        @click="onBack"
        :title="t('common.back')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-gray-600"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
      <h1 class="text-2xl font-bold text-gray-900">
        {{ t("admin.detail.title") }}
      </h1>
    </div>
    <AppLoader v-if="adminStore.loading" :text="t('common.loading')" />
    <template v-else-if="adminStore.currentUser">
      <AppCard>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          {{ t("admin.detail.userInfo") }}
        </h2>
        <dl class="grid grid-cols-2 gap-4">
          <div>
            <dt class="text-sm text-gray-500">{{ t("auth.name") }}</dt>
            <dd class="text-sm font-medium text-gray-900">
              {{ adminStore.currentUser.firstName }}
              {{ adminStore.currentUser.lastName }}
            </dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">{{ t("auth.email") }}</dt>
            <dd class="text-sm font-medium text-gray-900">
              {{ adminStore.currentUser.email }}
            </dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">{{ t("admin.columns.role") }}</dt>
            <dd>
              <AppBadge :variant="getRoleVariant(adminStore.currentUser.role)">
                {{ t(`admin.roles.${adminStore.currentUser.role}`) }}
              </AppBadge>
            </dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">
              {{ t("admin.columns.createdAt") }}
            </dt>
            <dd class="text-sm text-gray-900">
              {{ formatDate(adminStore.currentUser.createdAt) }}
            </dd>
          </div>
        </dl>
      </AppCard>
      <AppCard>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          {{ t("admin.detail.changeRole") }}
        </h2>
        <div class="flex items-end gap-4">
          <AppSelect
            v-model="selectedRole"
            :options="roleOptions"
            :label="t('admin.columns.role')"
          />
          <AppButton :disabled="!selectedRole" @click="onSaveRole">
            {{ t("admin.detail.save") }}
          </AppButton>
        </div>
      </AppCard>
      <AppCard>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          {{ t("admin.detail.sessions") }}
        </h2>
        <template v-if="adminStore.currentUser.sessions.length > 0">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b text-left text-gray-500">
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.ipAddress") }}
                </th>
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.userAgent") }}
                </th>
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.lastActivity") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="session in adminStore.currentUser.sessions"
                :key="session.id"
                class="border-b last:border-0"
              >
                <td class="py-2 text-gray-900">
                  {{ session.ipAddress || "-" }}
                </td>
                <td class="py-2 text-gray-500 max-w-xs truncate">
                  {{ session.userAgent || "-" }}
                </td>
                <td class="py-2 text-gray-500">
                  {{ formatDate(session.lastActivity) }}
                </td>
              </tr>
            </tbody>
          </table>
        </template>
        <p v-else class="text-sm text-gray-400">
          {{ t("admin.detail.noSessions") }}
        </p>
      </AppCard>
      <AppCard>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          {{ t("admin.detail.loginHistory") }}
        </h2>
        <template v-if="adminStore.currentUser.loginHistory.length > 0">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b text-left text-gray-500">
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.ipAddress") }}
                </th>
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.userAgent") }}
                </th>
                <th class="pb-2 font-medium">
                  {{ t("admin.detail.loggedInAt") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="entry in adminStore.currentUser.loginHistory"
                :key="entry.id"
                class="border-b last:border-0"
              >
                <td class="py-2 text-gray-900">
                  {{ entry.ipAddress || "-" }}
                </td>
                <td class="py-2 text-gray-500 max-w-xs truncate">
                  {{ entry.userAgent || "-" }}
                </td>
                <td class="py-2 text-gray-500">
                  {{ formatDate(entry.loggedInAt) }}
                </td>
              </tr>
            </tbody>
          </table>
        </template>
        <p v-else class="text-sm text-gray-400">
          {{ t("admin.detail.noLoginHistory") }}
        </p>
      </AppCard>
    </template>
  </div>
</template>
