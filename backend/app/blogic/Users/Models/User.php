<?php

declare(strict_types=1);

namespace App\blogic\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\blogic\Accounts\Models\Account;
use App\blogic\Applications\Models\Application;

/**
 * Modello per i dati utente (profilo).
 * Separato dall'account per segregare i dati personali.
 */
class User extends Model
{
    /**
     * Campi modificabili in massa.
     * @var array
     */
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'avatar_path',
    ];

    /**
     * Relazione molti-a-uno con Account.
     * Un profilo utente appartiene a un account.
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Relazione uno-a-molte con Application.
     * Un utente può avere più candidature.
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}