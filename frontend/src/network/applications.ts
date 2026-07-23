/**
 * Chiamate API per la gestione delle candidature
 */
import { apiGet, apiPost, apiPut, apiDelete } from "./api";
import type { Application, ApplicationCreate } from "@/types/application.types";
import type { PaginationMeta } from "@/types/shared.types";

/**
 * Risposta paginata dalle candidature
 */
export interface ApplicationResponse {
  data: Application[];
  meta?: PaginationMeta;
}

/**
 * Recupera lista paginata di candidature.
 * @param page - Numero pagina
 * @param companyId - Filtra per azienda (opzionale)
 * @returns Lista candidature con metadata paginazione
 */
export function getApplications(page = 1, companyId?: number) {
  let endpoint = `/applications?page=${page}`;
  if (companyId) endpoint += `&company_id=${companyId}`;
  return apiGet<ApplicationResponse>(endpoint);
}

/**
 * Recupera una singola candidatura con dettagli.
 * @param id - ID candidatura
 * @returns Dati candidatura
 */
export function getApplication(id: number) {
  return apiGet<{ data: Application }>(`/applications/${id}`);
}

/**
 * Crea una nuova candidatura.
 * @param data - Dati candidatura
 * @returns Candidatura creata
 */
export function createApplication(data: ApplicationCreate) {
  return apiPost<{ data: Application; message: string }>("/applications", data);
}

/**
 * Aggiorna una candidatura esistente.
 * @param id - ID candidatura
 * @param data - Dati aggiornati
 * @returns Candidatura aggiornata
 */
export function updateApplication(
  id: number,
  data: Partial<ApplicationCreate>,
) {
  return apiPut<{ data: Application; message: string }>(
    `/applications/${id}`,
    data,
  );
}

/**
 * Elimina una candidatura.
 * @param id - ID candidatura
 */
export function deleteApplication(id: number) {
  return apiDelete<void>(`/applications/${id}`);
}

/**
 * Azienda in sospeso, nominata in una candidatura ma non ancora creata.
 */
export interface PendingCompany {
  companyName: string;
  applicationCount: number;
}

/**
 * Recupera l'elenco delle aziende in sospeso.
 * Restituisce i nomi azienda inseriti manualmente nelle candidature,
 * raggruppati per nome con il conteggio delle candidature associate.
 * @returns Lista di aziende in attesa di creazione
 */
export function getPendingCompanies() {
  return apiGet<{ data: PendingCompany[] }>("/applications/pending-companies");
}

/**
 * Recupera l'elenco delle piattaforme distinte usate nelle candidature.
 * Usato per popolare la tendina di autocomplete nel form candidatura.
 * @returns Lista di nomi piattaforma
 */
export function getPlatforms() {
  return apiGet<{ data: string[] }>("/applications/platforms");
}
