import { defineStore } from "pinia";
import { ref } from "vue";
import * as adminApi from "@/network/admin";
import { toCamelCase } from "@/utils/transformers";
import type { AdminUser, AdminUserDetail } from "@/types/admin.types";
import type { PaginationMeta } from "@/types/shared.types";

export const useAdminStore = defineStore("admin", () => {
  const users = ref<AdminUser[]>([]);
  const currentUser = ref<AdminUserDetail | null>(null);
  const loading = ref(false);
  const pagination = ref<PaginationMeta | null>(null);

  /**
   * Recupera lista paginata di tutti gli utenti.
   */
  async function fetchUsers(page = 1) {
    loading.value = true;
    try {
      const res = await adminApi.getUsers(page);
      users.value = res.data.map((u) => toCamelCase<AdminUser>(u));
      pagination.value = res.meta
        ? toCamelCase<PaginationMeta>(res.meta)
        : null;
    } catch (err: any) {
      if (err.status !== 401) {
        throw err;
      }
    } finally {
      loading.value = false;
    }
  }

  /**
   * Recupera il dettaglio di un singolo utente con sessioni attive.
   */
  async function fetchUser(id: number) {
    loading.value = true;
    try {
      const res = await adminApi.getUser(id);
      currentUser.value = toCamelCase<AdminUserDetail>(res.data);
    } catch (err: any) {
      if (err.status !== 401) {
        throw err;
      }
    } finally {
      loading.value = false;
    }
  }

  /**
   * Aggiorna il ruolo di un utente e riflette la modifica nello store.
   */
  async function updateRole(id: number, role: string) {
    loading.value = true;
    try {
      await adminApi.updateUserRole(id, role);
      const index = users.value.findIndex((u) => u.id === id);
      if (index !== -1) users.value[index].role = role as AdminUser["role"];
      if (currentUser.value?.id === id)
        currentUser.value.role = role as AdminUser["role"];
    } catch (err: any) {
      if (err.status !== 401) {
        throw err;
      }
    } finally {
      loading.value = false;
    }
  }

  return {
    users,
    currentUser,
    loading,
    pagination,
    fetchUsers,
    fetchUser,
    updateRole,
  };
});
