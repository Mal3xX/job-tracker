/**
 * Contatto aziendale
 * Rappresenta una persona di riferimento all'interno di un'azienda
 */
export interface Contact {
    id: number;
    companyId: number;
    name: string;
    email: string | null;
    phone: string | null;
    role: string | null;
    linkedin: string | null;
    notes: string | null;
    isPrincipal?: boolean;
    creatorId?: number;
    createdAt: string;
    updatedAt: string;
}

/**
 * Payload per creare un nuovo contatto
 * Campi obbligatori e opzionali
 */
export interface ContactCreate {
    companyId: number;
    name: string;
    email?: string;
    phone?: string;
    role?: string;
    linkedin?: string;
    notes?: string;
}
