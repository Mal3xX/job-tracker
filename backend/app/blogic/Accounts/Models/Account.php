<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\blogic\Users\Models\User;

/**
 * Modello per gli account utente (autenticazione).
 * Separato dal modello User per segregare i dati sensibili (password).
 */
class Account extends Authenticatable
{
    use HasApiTokens;

    /**
     * Campi modificabili in massa.
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * Campi nascosti nella serializzazione JSON.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast dei tipi per la conversione automatica dei tipi.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relazione uno-a-uno con il modello User.
     * Un account ha un profilo utente associato.
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}