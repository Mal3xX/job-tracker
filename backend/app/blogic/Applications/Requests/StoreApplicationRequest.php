<?php

declare(strict_types=1);

namespace App\blogic\Applications\Requests;

use App\blogic\_Shared\Enums\ApplicationStatus;
use App\blogic\_Shared\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Validazione per la creazione di una nuova candidatura.
 */
class StoreApplicationRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a fare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per la creazione di una candidatura.
     */
    public function rules(): array
    {
        return [
            'company_id'   => 'nullable|integer|exists:companies,id',
            'company_name' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'work_mode' => ['nullable', new Enum(WorkMode::class)],
            'location' => 'nullable|string|max:255',
            'link_job' => 'nullable|string|max:1000',
            'platform' => 'nullable|string|max:255',
            'status' => ['nullable', new Enum(ApplicationStatus::class)],
            'interview_date' => 'nullable|date',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0|gte:salary_min',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Messaggi di errore personalizzati.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The position title is required.',
            'salary_max.gte' => 'The maximum salary must be greater than or equal to the minimum.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}
