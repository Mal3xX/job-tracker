/**
 * Converte le chiavi di un oggetto da snake_case a camelCase
 * @param obj - Oggetto da convertire
 * @returns Oggetto con chiavi in camelCase
 */
export function toCamelCase<T>(obj: any): T {
    if (Array.isArray(obj)) {
        return obj.map(item => toCamelCase(item)) as T;
    }

    if (obj !== null && typeof obj === 'object') {
        return Object.keys(obj).reduce((result, key) => {
            const camelKey = key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase());
            const value = obj[key];
            result[camelKey] = (value && typeof value === 'object')
                ? toCamelCase(value)
                : value;
            return result;
        }, {} as any);
    }

    return obj;
}