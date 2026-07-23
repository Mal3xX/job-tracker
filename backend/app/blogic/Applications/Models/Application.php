<?php

declare(strict_types=1);

namespace App\blogic\Applications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\blogic\Users\Models\User;
use App\blogic\Companies\Models\Company;

/**
 * Modello per le candidature di lavoro.
 * Rappresenta una candidatura inviata a un'azienda.
 */
class Application extends Model
{
    /**
     * Campi modificabili in massa.
     * @var array
     */
    protected $fillable = [
        'company_id',
        'company_name',
        'title',
        'work_mode',
        'location',
        'link_job',
        'platform',
        'status',
        'status_changed_at',
        'interview_date',
        'salary_min',
        'salary_max',
        'description',
        'notes',
    ];

    /**
     * Casts dei tipi per la conversione automatica dei campi
     * @var array
     */
    protected $casts = [
        'status_changed_at' => 'datetime',
        'interview_date' => 'date',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
    ];

    /**
     * Relazione molti-a-uno con User.
     * Una candidatura appartiene ad un utente.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione molti-a-uno con Company.
     * Una candidatura può essere associata a un'azienda.
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Determina se l'azienda associata alla candidatura è in sospeso.
     * Una candidatura è in sospeso quando ha un nome azienda inserito
     * manualmente ma non è ancora stata collegata a un'azienda reale.
     * @return bool True se l'azienda è in attesa di creazione
     */
    public function getIsPendingCompanyAttribute(): bool
    {
        return !is_null($this->company_name) && is_null($this->company_id);
    }
}
