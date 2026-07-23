<script setup lang="ts">
/**
 * Vista lista utenti admin con tabella, ricerca e paginazione.
 */
import { onMounted, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAdminStore } from "@/stores/useAdminStore";
import AppButton from "@/components/common/AppButton.vue";
import AppInput from "@/components/common/AppInput.vue";
import AppLoader from "@/components/common/AppLoader.vue";
import AppTable from "@/components/common/AppTable.vue";
import AppPagination from "@/components/common/AppPagination.vue";
import AppBadge from "@/components/common/AppBadge.vue";
import { formatDate } from "@/utils/formatters";
import type { AdminUser } from "@/types/admin.types";

const { t } = useI18n();
const router = useRouter();
const adminStore = useAdminStore();
const searchQuery = ref("");
const sortKey = ref("");
const sortOrder = ref<"asc" | "desc">("asc");

/**
 * Filtra gli utenti in base alla ricerca testuale.
 */
const filteredUsers = computed(() => {
  if (!searchQuery.value) return adminStore.users;
  const q = searchQuery.value.toLowerCase();
  return adminStore.users.filter(
    (u) =>
      `${u.firstName} ${u.lastName}`.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q),
  );
});

const columns = computed(() => [
  { key: "name", label: t("admin.columns.name"), sortable: true },
  { key: "email", label: t("admin.columns.email"), sortable: true },
  { key: "role", label: t("admin.columns.role") },
  { key: "sessions", label: t("admin.columns.sessions") },
  {
    key: "lastLogin",
    label: t("admin.columns.lastLogin"),
    sortable: true,
  },
  { key: "createdAt", label: t("admin.columns.createdAt"), sortable: true },
]);

onMounted(() => {
  adminStore.fetchUsers();
});

function onPageChange(page: number) {
  adminStore.fetchUsers(page);
}

function onViewUser(user: AdminUser) {
  router.push({ name: "admin-user-detail", params: { id: user.id } });
}

/**
 * Variante badge in base al ruolo.
 */
function getRoleVariant(role: string): "green" | "gray" {
  return role === "admin" ? "green" : "gray";
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">
        {{ t("admin.title") }}
      </h1>
    </div>
    <AppInput
      v-model="searchQuery"
      :placeholder="t('admin.searchPlaceholder')"
    />
    <AppLoader v-if="adminStore.loading" :text="t('admin.loading')" />
    <template v-else>
      <AppTable
        :columns="columns"
        :data="filteredUsers"
        :sort-key="sortKey"
        :sort-order="sortOrder"
        @sort="(key: string) => (sortKey = key)"
      >
        <template #cell-name="{ row }">
          <span class="font-medium text-gray-900">
            {{ row.firstName }} {{ row.lastName }}
          </span>
        </template>
        <template #cell-email="{ row }">
          <span class="text-gray-600">{{ row.email }}</span>
        </template>
        <template #cell-role="{ row }">
          <AppBadge :variant="getRoleVariant(row.role)">
            {{ t(`admin.roles.${row.role}`) }}
          </AppBadge>
        </template>
        <template #cell-sessions="{ row }">
          <span class="text-gray-600">
            {{ row.sessionCount }}
            <span v-if="row.sessionCount === 1" class="text-gray-400">{{
              t("admin.active")
            }}</span>
            <span v-else class="text-gray-400">{{
              t("admin.active_plural")
            }}</span>
          </span>
        </template>
        <template #cell-lastLogin="{ row }">
          <span class="text-gray-500">{{
            row.lastLoginAt ? formatDate(row.lastLoginAt) : "-"
          }}</span>
        </template>
        <template #cell-createdAt="{ row }">
          <span class="text-gray-500">{{ formatDate(row.createdAt) }}</span>
        </template>
        <template #actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <AppButton
              size="sm"
              variant="secondary"
              @click="onViewUser(row as AdminUser)"
            >
              {{ t("common.view") }}
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination
        v-if="adminStore.pagination"
        :current-page="adminStore.pagination.currentPage"
        :last-page="adminStore.pagination.lastPage"
        :total="adminStore.pagination.total"
        @page-change="onPageChange"
      />
    </template>
  </div>
</template>
