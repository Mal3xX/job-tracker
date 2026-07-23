/**
 * Chiamate API per la sezione admin.
 */
import { apiGet, apiPut } from './api'
import type { AdminUserDetail, AdminUsersResponse } from '@/types/admin.types'

/**
 * Recupera lista paginata di tutti gli utenti.
 * @param page - Numero pagina
 */
export function getUsers(page = 1) {
    return apiGet<AdminUsersResponse>(`/admin/users?page=${page}`)
}

/**
 * Recupera il dettaglio di un singolo utente
 * con profilo e sessioni attive.
 * @param id - ID utente
 */
export function getUser(id: number) {
    return apiGet<{ data: AdminUserDetail }>(`/admin/users/${id}`)
}

/**
 * Aggiorna il ruolo di un utente.
 * @param id - ID utente
 * @param role - Nuovo ruolo ('admin' | 'user')
 */
export function updateUserRole(id: number, role: string) {
    return apiPut<{ message: string }>(`/admin/users/${id}`, { role })
}