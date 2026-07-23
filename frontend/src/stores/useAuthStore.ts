import { defineStore } from "pinia";
import { ref, computed } from "vue";
import * as authApi from "@/network/auth";
import type { User, LoginRequest, RegisterRequest } from "@/types/auth.types";

export const useAuthStore = defineStore("auth", () => {
  const user = ref<User | null>(null);
  const loading = ref(false);

  const isAuthenticated = computed(() => user.value !== null);
  const isAdmin = computed(() => user.value?.role === "admin");

  /**
   * Effettua il login con email e password
   * Estrae dati utente dal wrapper "data" del backend
   */
  async function login(credentials: LoginRequest) {
    loading.value = true;
    try {
      await authApi.getCsrfCookie();
      const res = await authApi.login(credentials);
      const { account, user: userData } = res.data;
      user.value = {
        id: userData.id,
        firstName: userData.first_name,
        lastName: userData.last_name,
        email: account.email,
        role: account.role,
        avatarPath: userData.avatar_path ?? null,
      };
    } catch (err: any) {
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Registra un nuovo utente
   * Invia first_name e last_name al backend, poi salva utente
   */
  async function register(data: RegisterRequest) {
    loading.value = true;
    try {
      await authApi.getCsrfCookie();
      const res = await authApi.register(data);
      const { account, user: userData } = res.data;
      user.value = {
        id: userData.id,
        firstName: userData.first_name,
        lastName: userData.last_name,
        email: account.email,
        role: account.role,
        avatarPath: userData.avatar_path ?? null,
      };
    } catch (err: any) {
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Recupera i dati dell'utente corrente dal backend
   * Utile al refresh della pagina per ripristinare la sessione
   */
  async function fetchUser() {
    loading.value = true;
    try {
      const res = await authApi.getCurrentUser();
      const { account, user: userData } = res.data;
      user.value = {
        id: userData.id,
        firstName: userData.first_name,
        lastName: userData.last_name,
        email: account.email,
        role: account.role,
        avatarPath: userData.avatar_path ?? null,
      };
    } catch (err: any) {
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Resetta lo stato locale dell'utente senza chiamare il backend.
   * Usato nell'handler 401 per evitare il loop infinito
   * (il backend ha già risposto "non autenticato", non serve chiamare logout).
   */
  function clearAuth() {
    user.value = null;
  }

  /**
   * Aggiorna il profilo dell'utente autenticato.
   * Supporta cambio email, password e upload avatar tramite FormData.
   * @param data - FormData con i campi da aggiornare
   */
  async function updateProfile(data: FormData) {
    loading.value = true;
    try {
      const res = await authApi.updateProfile(data);
      const { account, user: userData } = res.data;
      user.value = {
        id: userData.id,
        firstName: userData.first_name,
        lastName: userData.last_name,
        email: account.email,
        role: account.role,
        avatarPath: userData.avatar_path ?? null,
      };
    } catch (err: any) {
      throw err;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Effettua il logout: pulisce utente ed errori
   */
  function logout() {
    authApi.logout().catch(() => {});
    user.value = null;
  }

  return {
    user,
    loading,
    isAuthenticated,
    isAdmin,
    updateProfile,
    login,
    register,
    fetchUser,
    logout,
    clearAuth,
  };
});
