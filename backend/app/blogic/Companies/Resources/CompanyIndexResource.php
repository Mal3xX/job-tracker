<?php

declare(strict_types=1);

namespace App\blogic\Companies\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per la lista aziende (index)
 * Restituisce campi essenziali per la visualizzazione in lista.
 */
class CompanyIndexResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sector' => $this->sector,
            'size' => $this->size,
            'website' => $this->website,
            'logo_path' => $this->logo_path,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}