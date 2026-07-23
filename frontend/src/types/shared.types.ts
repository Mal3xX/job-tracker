/**
 * Metadata sulla paginazione restituita dal backend.
 * Contiene informazioni su pagina corrente, ultima pagina, 
 * numero di elementi per pagina e totale elementi.
 */
export interface PaginationMeta {
    currentPage: number;
    from: number;
    lastPage: number;
    perPage: number;
    to: number;
    total: number;
}

/**
 * Struttura di un errore API
 * Restituita quando la richiesta fallisce con codice 4xx o 5xx
 */
export interface ApiError {
    message: string;
    status?: number;
    errors?: Record<string, string[]>;
}
