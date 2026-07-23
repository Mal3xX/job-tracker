/**
 * Chiamate API per la gestione delle aziende
 */
import { apiGet, apiPost, apiPut, apiDelete } from "./api";
import type {
  Company,
  CompanyCreate,
  CompanyResponse,
} from "@/types/company.types";

/**
 * Recupera lista paginata di aziende.
 * @param page - Numero pagina
 * @returns Lista aziende con metadata paginazione
 */
export function getCompanies(page = 1) {
  return apiGet<CompanyResponse>(`/companies?page=${page}`);
}

/**
 * Recupera una singola azienda con dettagli.
 * @param id - ID azienda
 * @returns Dati azienda
 */
export function getCompany(id: number) {
  return apiGet<{ data: Company }>(`/companies/${id}`);
}

/**
 * Recupera tutte le aziende (solo ID e nome) per autocomplete.
 */
export function getCompanyNames() {
  return apiGet<{ data: { id: number; name: string }[] }>("/companies/names");
}

/**
 * Crea una nuova azienda.
 * @param data - Dati azienda
 * @returns Azienda creata
 */
export function createCompany(data: CompanyCreate) {
  return apiPost<{ data: Company; message: string }>("/companies", data);
}

/**
 * Aggiorna un'azienda esistente.
 * @param id - ID azienda
 * @param data - Dati aggiornati
 * @returns Azienda aggiornata
 */
export function updateCompany(id: number, data: Partial<CompanyCreate>) {
  return apiPut<{ data: Company; message: string }>(`/companies/${id}`, data);
}

/**
 * Elimina un'azienda.
 * @param id - ID azienda
 */
export function deleteCompany(id: number) {
  return apiDelete<void>(`/companies/${id}`);
}
