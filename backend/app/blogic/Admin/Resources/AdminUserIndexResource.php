<?php

declare(strict_types=1);

namespace App\blogic\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * Resource per la lista utenti admin (index).
 * Include dati account, profilo e ultimo accesso.
 */
class AdminUserIndexResource extends JsonResource
{
    /**
     * Trasforma la risorsa in array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->role,
            'first_name' => $this->user?->first_name,
            'last_name' => $this->user?->last_name,
            'last_login_at' => $this->last_login_at
                ? Carbon::parse($this->last_login_at)->toISOString()
                : null,
            'session_count' => (int) $this->session_count,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
