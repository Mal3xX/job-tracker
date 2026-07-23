/**
 * Converte le chiavi di un oggetto da camelCase a snake_case
 * @param obj - Oggetto da convertire
 * @returns Oggetto con chiavi in snake_case
 */

export function toSnakeCase<T>(obj: any): T {
  if (Array.isArray(obj)) {
    return obj.map(item => toSnakeCase(item)) as T;
  }
  
  if (obj !== null && typeof obj === 'object') {
    return Object.keys(obj).reduce((result, key) => {
      const snakeKey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
      const value = obj[key];
      result[snakeKey] = (value && typeof value === 'object' && !Array.isArray(value))
        ? toSnakeCase(value)
        : value;
      return result;
    }, {} as any);
  }
  
  return obj;
}