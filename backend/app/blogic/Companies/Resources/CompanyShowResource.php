<?php

declare(strict_types=1);

namespace App\blogic\Companies\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per il dettaglio di un'azienda (show)
 * Include tutti i campi più i contatti più il conteggio delle candidature
 */
class CompanyShowResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
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
            'linkedin' => $this->linkedin,
            'description' => $this->description,
            'logo_path' => $this->logo_path,
            'notes' => $this->notes,
            'creator_id' => $this->creator_id,
            'contacts' => ContactIndexResource::collection($this->whenLoaded('contacts')),
            'applications_count' => $this->whenCounted('applications'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}