<?php

declare(strict_types=1);

namespace App\blogic\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\blogic\Companies\Models\Contact;
use App\blogic\Applications\Models\Application;
use App\blogic\Users\Models\User;

/**
 * Modello per le aziende.
 * Rappresenta le aziende dove l'utente ha inviato candidature.
 */
class Company extends Model
{
    /**
     * Campi modificabili in massa
     * @var array
     */
    protected $fillable = [
        'name',
        'sector',
        'size',
        'website',
        'linkedin',
        'description',
        'logo_path',
        'notes',
    ];

    /**
     * Relazione uno-a-molte con Contact.
     * Un'azienda può avere più contatti.
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Relazione uno-a-molte con Application.
     * Un'azienda può avere più candidature.
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Relazione molti-a-uno con User (creatore).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}