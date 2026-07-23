<?php

declare(strict_types=1);

namespace App\blogic\Admin\Resources;

use App\blogic\Accounts\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Resource per il dettaglio di un utente admin (show).
 * Include tutti i dati account, profilo e sessioni attive.
 */
class AdminUserShowResource extends JsonResource
{
    private Collection $sessions;
    private Collection $loginHistory;

    public function __construct(Account $resource, Collection $sessions, Collection $loginHistory)
    {
        parent::__construct($resource);
        $this->sessions     = $sessions;
        $this->loginHistory = $loginHistory;
    }

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
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'sessions' => $this->sessions->map(fn($session) => [
                'id'            => $session->id,
                'ip_address'    => $session->ip_address,
                'user_agent'    => $session->user_agent,
                'last_activity' => Carbon::createFromTimestamp($session->last_activity)->toISOString(),
            ])->values(),
            'login_history' => $this->loginHistory->map(fn($entry) => [
                'id'           => $entry->id,
                'ip_address'   => $entry->ip_address,
                'user_agent'   => $entry->user_agent,
                'logged_in_at' => Carbon::parse($entry->created_at)->toISOString(),
            ])->values(),
        ];
    }
}
