<?php

declare(strict_types=1);

namespace App\blogic\Companies\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per il dettaglio contatto.
 */
class ContactShowResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
            'phone' => $this->phone,
            'linkedin' => $this->linkedin,
            'is_principal' => $this->is_principal,
            'creator_id' => $this->creator_id,
            'notes' => $this->notes,
            'company' => $this->whenLoaded('company', fn() => [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
