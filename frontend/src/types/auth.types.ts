/**
 * Utente autenticato nel sistema
 * Rappresenta l'account corrente con le sue informazioni
 */
export interface User {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    role: 'admin' | 'operator' | 'user';
    /** Percorso dell'immagine profilo (storage pubblico), null se non impostata */
    avatarPath?: string | null;
}

/**
 * Payload per la richiesta di login
 * L'utente fornisce email e password per autenticarsi
 */
export interface LoginRequest {
    email: string;
    password: string;
}

/**
 * Risposta dopo un login effettuato con successo
 * Il backend restituisce i dati annidati in un wrapper "data"
 */
export interface LoginResponse {
    data: {
        account: {
            id: number;
            email: string;
            role: 'admin' | 'operator' | 'user';
        };
        user: {
            id: number;
            first_name: string;
            last_name: string;
            /** Percorso dell'immagine profilo nel disco pubblico */
            avatar_path: string | null;
        };
    };
}

/**
 * Payload per la registrazione di un nuovo account
 * L'utente fornisce i dati per creare un nuovo profilo
 * Il backend richiede first_name e last_name separati
 */
export interface RegisterRequest {
    email: string;
    password: string;
    password_confirmation: string;
    first_name: string;
    last_name: string;
}

/**
 * Risposta dal backend per il recupero dell'utente corrente
 * Struttura annidata in un wrapper "data" con account e user separati
 */
export interface MeResponse {
    data: {
        account: {
            id: number;
            email: string;
            role: 'admin' | 'operator' | 'user';
        };
        user: {
            id: number;
            first_name: string;
            last_name: string;
            /** Percorso dell'immagine profilo nel disco pubblico */
            avatar_path: string | null;
        };
    };
}

/**
 * Risposta dopo l'aggiornamento del profilo
 * Stessa struttura di MeResponse, restituita dal backend
 */
export interface UpdateProfileResponse {
    data: {
        account: {
            id: number;
            email: string;
            role: 'admin' | 'operator' | 'user';
            email_verified_at: string | null;
        };
        user: {
            id: number;
            first_name: string;
            last_name: string;
            /** Percorso aggiornato dell'immagine profilo */
            avatar_path: string | null;
        };
    };
}