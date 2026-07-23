<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Enums;

/**
 * Enum per la dimensione delle aziende.
 * Definisce le categorie di grandezza: piccola, media, grande.
 */
enum CompanySize: string
{
    case SMALL = 'small'; //Piccola (1-50 dipendenti)
    case MEDIUM = 'medium'; //Media (51-250 dipendenti)
    case LARGE = 'large'; //Grande (250+ dipendenti)

    /**
     * Restituisce l'etichetta leggibile per l'utente.
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::SMALL => 'Small',
            self::MEDIUM => 'Medium',
            self::LARGE => 'Large',
        };
    }
}