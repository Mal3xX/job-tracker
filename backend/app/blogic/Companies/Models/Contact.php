<?php

declare(strict_types=1);

namespace App\blogic\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\blogic\Companies\Models\Company;
use App\blogic\Users\Models\User;

/**
 * Modello per i contatti aziendali.
 * Rappresenta le persone di riferimento all'interno di un'azienda.
 */
class Contact extends Model
{
    /**
     * Campi modificabili in massa.
     * @var array
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'phone',
        'linkedin',
        'is_principal',
        'notes',
    ];

    /**
     * Campi nascosti nella serializzazione JSON
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Cast dei tipi per la conversione automatica dei campi.
     * @var array
     */
    protected $casts = [
        'is_principal' => 'boolean',
    ];

    /**
     * Relazione molti-a-uno con Company.
     * Un contatto appartiene a un'azienda.
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relazione molti-a-uno con User (creatore).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}