<?php

declare(strict_types=1);

namespace App\blogic\Applications\Requests;

use App\blogic\_Shared\Enums\ApplicationStatus;
use App\blogic\_Shared\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Validazione per l'aggiornamento di una candidatura esistente.
 */
class UpdateApplicationRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a fare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per l'aggiornamento.
     * Tutti i campi sono opzionali per permettere aggiornamenti parziali.
     */
    public function rules(): array
    {
        return [
            'company_id'   => 'nullable|integer|exists:companies,id',
            'company_name' => 'nullable|string|max:255',
            'title' => 'sometimes|string|max:255',
            'work_mode' => ['sometimes', 'nullable', new Enum(WorkMode::class)],
            'location' => 'sometimes|nullable|string|max:255',
            'link_job' => 'sometimes|nullable|string|max:1000',
            'platform' => 'sometimes|nullable|string|max:255',
            'status' => ['sometimes', 'nullable', new Enum(ApplicationStatus::class)],
            'interview_date' => 'sometimes|nullable|date',
            'salary_min' => 'sometimes|nullable|integer|min:0',
            'salary_max' => 'sometimes|nullable|integer|min:0|gte:salary_min',
            'description' => 'sometimes|nullable|string',
            'notes' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Messaggi di errore personalizzati.
     */
    public function messages(): array
    {
        return [
            'salary_max.gte' => 'The maximum salary must be greater than or equal to the minimum.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}
