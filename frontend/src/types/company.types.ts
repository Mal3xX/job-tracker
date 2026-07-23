// Import necessario per CompanyResponse
import type { PaginationMeta } from "./shared.types";
import type { Contact } from "./contact.types";

/**
 * Azienda nel sistema
 * Rappresenta un'azienda a cui l'utente può candidarsi o avere contatti
 */
export interface Company {
    id: number;
    name: string;
    slug: string;
    sector: string | null;
    size: 'small' | 'medium' | 'large' | null;
    website: string | null;
    linkedin: string | null;
    description: string | null;
    notes?: string | null;
    logoPath: string | null;
    applicationsCount?: number;
    contacts?: Contact[];
    creatorId?: number;
    createdAt: string;
    updatedAt: string;
}

/**
 * Payload per creare una nuova azienda
 * Campi obbligatori e opzionali per la creazione
 */
export interface CompanyCreate {
    name: string;
    sector?: string;
    size?: 'small' | 'medium' | 'large';
    website?: string;
    linkedin?: string;
    description?: string;
    notes?: string;
}

/**
 * Risposta paginata dalle aziende
 * Utilizzata per le liste di aziende con paginazione
 */
export interface CompanyResponse {
    data: Company[];
    meta?: PaginationMeta;
}
