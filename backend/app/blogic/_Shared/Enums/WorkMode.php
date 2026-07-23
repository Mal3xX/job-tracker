<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Enums;

/**
 * Enum per la modalità di lavoro
 * Definisce le opzioni: remoto, in sede, ibrido.
 */
enum WorkMode: string
{
    case REMOTE = 'remote';
    case OFFICE = 'office';
    case HYBRID = 'hybrid';

    /**
     * Restituisce l'etichetta leggibile per l'utente
     * @return string
     */
    public function label(): string 
    {
        return match ($this) {
            self::REMOTE => 'Remote',
            self::OFFICE => 'On-Site',
            self::HYBRID => 'Hybrid',
        };
    }
}
