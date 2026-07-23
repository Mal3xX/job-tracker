import type { Company } from "./company.types"

/**
 * Modalità di lavoro disponibili
 * Indica se il lavoro è da remoto, in ufficio o ibrido
 */
export type WorkMode = 'remote' | 'office' | 'hybrid';

/**
 * Stato della candidature
 * Rappresenta lo stato attuale nel processo di recruiting
 */
export type ApplicationStatus = 
    | 'pending'
    | 'negative'
    | 'positive'
    | 'interview'
    | 'offer'
    | 'no_response';

/**
 * Candidatura lavorativa
 * Rappresenta una candidatura a un annuncio di lavoro
 */
export interface Application {
    id: number;
    userId: number;
    companyId: number | null;
    companyName: string | null;
    company?: Company;
    title: string;
    workMode: WorkMode | null;
    location: string | null;
    linkJob: string | null;
    platform: string | null;
    status: ApplicationStatus;
    statusChangedAt: string | null;
    interviewDate: string | null;
    salaryMin: number | null;
    salaryMax: number | null;
    description?: string | null;
    notes: string | null;
    createdAt: string;
    updatedAt: string;
}

/**
 * Payload per creare una nuova candidatura
 * Campi obbligatori e opzionali
 */
export interface ApplicationCreate {
    companyId?: number;
    companyName?: string; 
    title: string;
    workMode?: WorkMode;
    location?: string;
    linkJob?: string;
    platform?: string;
    status?: ApplicationStatus;
    interviewDate?: string;
    salaryMin?: number;
    salaryMax?: number;
    description?: string;
    notes?: string;
}
