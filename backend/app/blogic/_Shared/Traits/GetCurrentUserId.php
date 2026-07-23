<?php

declare(strict_types=1);

namespace App\blogic\_Shared\Traits;

use Illuminate\Support\Facades\Auth;
use RuntimeException;

trait GetCurrentUserId
{
    private function getCurrentUserId(): int
    {
        $account = Auth::user();
        if (!$account || !$account->user) {
            throw new RuntimeException(__('auth.user_not_found'));
        }
        return $account->user->id;
    }
}
