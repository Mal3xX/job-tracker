<?php

declare(strict_types=1);

namespace App\blogic\Applications\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per la lista delle candidature (index).
 * Restituisce campi essenziali per la visualizzazione in lista.
 */
class ApplicationIndexResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id, 
            'title' => $this->title,
            'company_name' => $this->company_name,
            'work_mode' => $this->work_mode,
            'location' => $this->location,
            'platform' => $this->platform,
            'status' => $this->status,
            'status_changed_at' => $this->status_changed_at?->toISOString(),
            'interview_date' => $this->interview_date?->toISOString(),
            'company' => $this->whenLoaded('company') && $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'slug' => $this->company->slug,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
