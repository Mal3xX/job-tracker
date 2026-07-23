<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Servizio per la generazione di slug URL-friendly.
 * Utilizzato per creare slug univoci per aziende e altri entity.
 */
class SlugService
{
    /**
     * Genera uno slug da una stringa.
     * @param string $value La stringa da convertire in slug
     * @return string Lo slug generato
     */
    public function generate(string $value): string
    {
        return Str::slug($value);
    }

    /**
     * Genera uno slug univoco controllando il database.
     * Se lo slug esiste già, aggiunge un contatore progressivo.
     * @param string $value La stringa base per lo slug
     * @param string $table Nome della tabella dove verificare l'univocità
     * @param string $column Nome della colonna slug nella tabella
     * @return string Lo slug univoco
     */
    public function generateUnique(string $value, string $table, string $column = 'slug'): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 1;
        $maxAttempts = 100;

        while ($counter <= $maxAttempts) {
            $exists = DB::table($table)->where($column, $slug)->exists();
            if (!$exists) {
                return $slug;
            }
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        throw new RuntimeException('Unable to generate unique slug for: ' . $value);
    }
}