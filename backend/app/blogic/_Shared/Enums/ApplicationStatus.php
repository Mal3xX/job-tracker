<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Enums;

/**
 * Enum per lo stato delle candidature
 * Definisce i possibili stati: in attesa, negativo, positivo, colloquio, offerta, nessuna risposta.
 */
enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case NEGATIVE = 'negative';
    case POSITIVE = 'positive';
    case INTERVIEW = 'interview';
    case OFFER = 'offer';
    case NO_RESPONSE = 'no_response';

    /**
     * Restituisce l'etichetta leggibile per l'utente
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::NEGATIVE => 'Negative',
            self::POSITIVE => 'Positive',
            self::INTERVIEW => 'Interview',
            self::OFFER => 'Offer',
            self::NO_RESPONSE => 'No Response',
        };
    }
}