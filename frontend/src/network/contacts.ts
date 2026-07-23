/**
 * Chiamate API per la gestione dei contatti
 */
import { apiGet, apiPost, apiPut, apiDelete } from './api'
import type { Contact, ContactCreate } from '@/types/contact.types'

/**
 * Recupera lista contatti di un'azienda.
 * @param companyId - ID dell'azienda
 * @returns Lista contatti
 */
export function getContacts(companyId: number) {
    return apiGet<{ data: Contact[] }>(`/companies/${companyId}/contacts`)
}

/**
 * Crea un nuovo contatto per un'azienda.
 * @param companyId - ID dell'azienda
 * @param data - Dati contatto
 * @returns Contatto creato
 */
export function createContact(companyId: number, data: ContactCreate) {
    return apiPost<{ data: Contact; message: string }>(`/companies/${companyId}/contacts`, data)
}

/**
 * Aggiorna un contatto esistente.
 * @param id - ID del contatto
 * @param data - Dati aggiornati
 * @returns Contatto aggiornato
 */
export function updateContact(id: number, data: Partial<ContactCreate>) {
    return apiPut<{ data: Contact; message: string }>(`/contacts/${id}`, data)
}

/**
 * Elimina un contatto.
 * @param id - ID del contatto
 */
export function deleteContact(id: number) {
    return apiDelete<void>(`/contacts/${id}`)
}