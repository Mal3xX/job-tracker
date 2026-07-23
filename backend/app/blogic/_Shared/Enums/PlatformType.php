<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Enums;

/**
 * Enum per le piattaforme di ricerca lavoro.
 * Definisce le piattaforme supportate per la ricerca di annunci.
 */
enum PlatformType: string
{
    case LINKEDIN = 'linkedin';
    case INFOJOBS = 'infojobs';
    case INDEED = 'indeed';
    case SUBITO = 'subito';
    case GLASSDOOR = 'glassdoor';
    case WORKDAY = 'workday';
    case MONSTER = 'monster';
    case GREENHOUSE = 'greenhouse';
    case OTHER = 'other';

    /**
     * Restituisce l'etichetta leggibile per l'utente.
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::LINKEDIN => 'LinkedIn',
            self::INFOJOBS => 'InfoJobs',
            self::INDEED => 'Indeed',
            self::SUBITO => 'Subito',
            self::GLASSDOOR => 'Glassdoor',
            self::MONSTER => 'Monster',
            self::WORKDAY => 'Workday',
            self::GREENHOUSE => 'Greenhouse',
            self::OTHER => 'Other',
        };
    }
}