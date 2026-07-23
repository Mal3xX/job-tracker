import { toCamelCase } from "./toCamelCase";
import { toSnakeCase } from "./toSnakeCase";

/**
 * Trasforma un array di oggetti in camelCase
 */
export function transformToCamelCase<T>(items: any[]): T {
    return items.map(item => toCamelCase(item)) as T;
}

/**
 * Trasforma un array di oggetti in snake_case
 */
export function transformToSnakeCase<T>(items: any[]): T {
    return items.map(item => toSnakeCase(item)) as T;
}