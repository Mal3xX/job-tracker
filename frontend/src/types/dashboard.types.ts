import type { Application } from "./application.types";

/**
 * Statistiche della dashboard
 * Rappresenta tutti i dati statistici mostrati nella home dell'utente
 */
export interface DashboardStats {
    // Statistiche generali
    totalApplications: number;
    currentMonthApplications: number;
    previousMonthApplications: number;

    // Andamento mensile
    trend: {
        month: string;
        count: number;
    }[];

    // Distribuzione per stato
    statusDistribution: {
        status: string;
        count: number;
    }[];

    // Ultime cinque candudature
    latestApplications: Application[];

    // Tempo medio di risposta in giorni
    averageResponseTime: number | null;
}
