import type { PaginationMeta } from "./shared.types";

/**
 * Utente visualizzato nella lista admin (index).
 */
export interface AdminUser {
    id: number;
    email: string;
    role: 'admin' | 'user';
    firstName: string | null;
    lastName: string | null;
    lastLoginAt: string | null;
    sessionCount: number;
    createdAt: string;
}

/**
 * Utente con dettagli completi e sessioni (show).
 */
export interface AdminUserDetail extends AdminUser {
    updatedAt: string;
    sessions: AdminSession[];
    loginHistory: LoginHistoryEntry[];
}

/**
 * Sessione attiva di un utente.
 */
export interface AdminSession {
    id: string;
    ipAddress: string | null;
    userAgent: string | null;
    lastActivity: string;
}

/**
 * Voce dello storico accessi di un utente.
 */
export interface LoginHistoryEntry {
    id: number;
    ipAddress: string | null;
    userAgent: string | null;
    loggedInAt: string;
}

/**
 * Risposta paginata per la lista utenti admin.
 */
export interface AdminUsersResponse {
    data: AdminUser[];
    meta: PaginationMeta;
}