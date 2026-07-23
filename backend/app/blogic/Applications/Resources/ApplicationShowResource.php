<?php

declare(strict_types=1);

namespace App\blogic\Applications\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per il dettaglio di una candidatura (show).
 * Restituisce tutti i campi inclusi dati azienda completi.
 */
class ApplicationShowResource extends JsonResource
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
            'company_name' => $this->company_name,
            'title' => $this->title,
            'work_mode' => $this->work_mode,
            'location' => $this->location,
            'link_job' => $this->link_job,
            'platform' => $this->platform,
            'status' => $this->status,
            'status_changed_at' => $this->status_changed_at?->toISOString(),
            'interview_date' => $this->interview_date?->toISOString(),
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'description' => $this->description,
            'notes' => $this->notes,
            'company' => $this->whenLoaded('company') && $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'slug' => $this->company->slug,
                'sector' => $this->company->sector,
                'size' => $this->company->size,
                'website' => $this->company->website,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
