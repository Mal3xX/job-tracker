/**
 * Chiamate API per la dashboard
 */
import { apiGet } from './api'
import type { DashboardStats } from '@/types/dashboard.types'

/**
 * Recupera statistiche dashboard.
 * @returns Statistiche aggregate delle candidature
 */
export async function getStats() {
    const res = await apiGet<{ data: DashboardStats }>('/dashboard/stats')
    return res.data
}