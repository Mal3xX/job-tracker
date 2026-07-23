<?php

declare(strict_types=1);

namespace App\blogic\Applications\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per la lista di aziende candidate non ancora create.
 */
class PendingCompanyResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return [
            'company_name' => $this->company_name,
            'application_count' => (int) $this->application_count,
        ];
    }
}
