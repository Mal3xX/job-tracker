<?php

declare(strict_types=1);

namespace App\blogic\Companies\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource per la lista contatti.
 */
class ContactIndexResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'email' => $this->email,
            'phone' => $this->phone,
            'linkedin' => $this->linkedin,
            'company_id' => $this->company_id,
            'creator_id' => $this->creator_id,
            'notes' => $this->notes,
            'is_principal' => $this->is_principal,
        ];
    }
}