/**
 * wrapper fetch centrale per tutte le chiamate API
 * Gestisce credentials cookie-based, parsing JSON, error handling e redirect su 401
 */
import i18n from "@/plugins/i18n";
import router from "@/router";
import { API_CONFIG } from "@/utils/constants";
import type { ApiError } from "@/types/shared.types";
import { toSnakeCase } from "@/utils/transformers";
import { useAuthStore } from "@/stores/useAuthStore";

export type HttpMethod = "GET" | "POST" | "PUT" | "PATCH" | "DELETE";

interface ApiCallOptions {
  method: HttpMethod;
  data?: unknown;
  headers?: Record<string, string>;
  isFormData?: boolean;
}

/**
 * Legge il token CSRF dal cookie XSRF-TOKEN impostato da Laravel/Sanctum.
 */
function getXsrfToken(): string | null {
  const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : null;
}

/**
 * Esegue una chiamata API al backend
 * @param endpoint - Path dell'endpoint API
 * @param options - Opzioni della richiesta (method, data, headers)
 * @returns Promise con la risposta API
 * @throws Reinderizza a /login su 401, lancia ApiError altrimenti
 */
export async function apiCall<T>(
  endpoint: string,
  { method, data, headers = {}, isFormData }: ApiCallOptions,
): Promise<T> {
  const rawBaseUrl = import.meta.env.VITE_API_BASE_URL as string;
  const isSanctumEndpoint = endpoint.startsWith("/sanctum/");
  const baseUrl = isSanctumEndpoint
    ? rawBaseUrl.replace(/\/api\/v1\/?$/, "")
    : rawBaseUrl;

  const requestHeaders: Record<string, string> = {
    "Content-Type": "application/json",
    Accept: "application/json",
    ...headers,
  };

  const xsrfToken = getXsrfToken();
  if (xsrfToken) {
    requestHeaders["X-XSRF-TOKEN"] = xsrfToken;
  }

  const config: RequestInit = {
    method,
    headers: requestHeaders,
    credentials: "include",
  };

  if (data && method !== "GET") {
    if (isFormData) {
      delete requestHeaders["Content-Type"];
      config.body = data as BodyInit;
    } else {
      config.body = JSON.stringify(toSnakeCase(data));
    }
  }

  const controller = new AbortController();
  config.signal = controller.signal;

  const timeoutId = setTimeout(() => controller.abort(), API_CONFIG.TIMEOUT);

  try {
    const response = await fetch(`${baseUrl}${endpoint}`, config);
    clearTimeout(timeoutId);
    if (response.status === 401) {
      useAuthStore().clearAuth();
      router.push("/login");
      throw new Error("Unauthorized");
    }

    if (
      response.status === 204 ||
      response.headers.get("content-length") === "0" ||
      response.body === null
    ) {
      return undefined as T;
    }

    const result = await response.json();
    if (!response.ok) {
      const error: ApiError = {
        message: result.message || i18n.global.t("errors.generic"),
        status: response.status,
        errors: result.errors,
      };
      throw error;
    }
    return result as T;
  } catch (error) {
    clearTimeout(timeoutId);
    if (error instanceof Error && error.name === "AbortError") {
      throw new Error(i18n.global.t("errors.timeout"));
    }
    throw error;
  }
}

/**
 * Shorthand per richieste GET.
 */
export function apiGet<T>(endpoint: string): Promise<T> {
  return apiCall<T>(endpoint, { method: "GET" });
}

/**
 * Shorthand per richieste POST.
 */
export function apiPost<T>(
  endpoint: string,
  data: unknown,
  isFormData = false,
): Promise<T> {
  return apiCall<T>(endpoint, { method: "POST", data, isFormData });
}

/**
 * Shorthand per richieste PUT.
 */
export function apiPut<T>(endpoint: string, data: unknown, isFormData = false): Promise<T> {
    return apiCall<T>(endpoint, { method: 'PUT', data, isFormData })
}

/**
 * Shorthand per richieste PATCH.
 */
export function apiPatch<T>(endpoint: string, data: unknown, isFormData = false): Promise<T> {
    return apiCall<T>(endpoint, { method: 'PATCH', data, isFormData })
}

/**
 * Shorthand per richieste DELETE.
 */
export function apiDelete<T>(endpoint: string): Promise<T> {
  return apiCall<T>(endpoint, { method: "DELETE" });
}
