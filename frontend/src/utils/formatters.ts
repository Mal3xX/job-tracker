import i18n from '@/plugins/i18n'

const localeMap: Record<string, string> = { it: 'it-IT', en: 'en-US' }

/**
 * Formatta un numero con zero padding (es. 3 → "03")
 */
function pad(n: number): string {
    return String(n).padStart(2, '0');
}

/**
 * Formatta una data in formato leggibile
 * - Italiano: DD/MM/YYYY
 * - Inglese: YYYY-MM-DD (ISO)
 */
export function formatDate(date: string | Date): string {
    const d = new Date(date);
    const locale = i18n.global.locale.value;
    if (locale === 'en') {
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
}

/**
 * Formatta una data relativa 
 */
export function formatRelativeDate(date: string | Date): string {
  const now = new Date();
  const then = new Date(date);
  const diffMs = now.getTime() - then.getTime();
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) return i18n.global.t('date.today');
  if (diffDays === 1) return i18n.global.t('date.yesterday');
  if (diffDays < 7) return i18n.global.t('date.daysAgo', { days: diffDays });
  return formatDate(date);
}

/**
 * Formatta un importo in valuta
 */
export function formatCurrency(amount: number, currency: string = 'EUR'): string {
  const locale = localeMap[i18n.global.locale.value] || 'it-IT'
  return new Intl.NumberFormat(locale, { style: 'currency', currency }).format(amount);
}

/**
 * Tronca un testo alla lunghezza specificata
 */
export function truncate(text: string, length: number): string {
  return text.length > length ? text.substring(0, length) + '...' : text;
}

/**
 * Prima lettera maiuscola
 */
export function capitalize(text: string): string {
  return text.charAt(0).toUpperCase() + text.slice(1);
}