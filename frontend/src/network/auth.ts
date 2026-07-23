/**
 * Chiamate API per l'autenticazione
 */
import { apiGet, apiPost } from "./api";
import type {
  LoginRequest,
  LoginResponse,
  RegisterRequest,
  MeResponse,
  UpdateProfileResponse,
} from "@/types/auth.types";

/**
 * Richiede il cookie CSRF da Sanctum.
 * Necessario prima di login/register con sessioni stateful.
 */
export function getCsrfCookie(): Promise<void> {
  return apiGet("/sanctum/csrf-cookie");
}

/**
 * Effettua il login dell'utente.
 * @param credentials - Email e password
 * @returns Dati risposta con utente
 */
export function login(credentials: LoginRequest) {
  return apiPost<LoginResponse>("/auth/login", credentials);
}

/**
 * Effettua il logout dell'utente.
 */
export function logout() {
  return apiPost<void>("/auth/logout", {});
}

/**
 * Recupera l'utente corrente tramite sessione.
 * @returns Dati dell'utente autenticato
 */
export function getCurrentUser() {
  return apiGet<MeResponse>("/auth/me");
}

/**
 * Registra un nuovo utente.
 * @param data - Dati di registrazione
 * @returns Dati risposta con utente
 */
export function register(data: RegisterRequest) {
  return apiPost<LoginResponse>("/auth/register", data);
}

/**
 * Aggiorna il profilo dell'utente autenticato.
 * Invia i dati come FormData per supportare l'upload dell'avatar.
 * @param data - FormData con email, password, avatar (tutti opzionali)
 * @returns Dati aggiornati dell'utente
 */
export function updateProfile(data: FormData) {
  return apiPost<UpdateProfileResponse>("/auth/profile", data, true);
}