<?php

declare(strict_types=1);

namespace App\blogic\Companies\Requests;

use App\blogic\_Shared\Enums\CompanySize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Validazione per la creazione di una nuova azienda.
 */
class StoreCompanyRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a fare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per la creazione di un azienda.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sector' => 'nullable|string|max:255',
            'size' => ['nullable', new Enum(CompanySize::class)],
            'website' => 'nullable|string|max:1000',
            'logo_path' => 'nullable|string|max:1000',
            'linkedin' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Messaggi di errore personalizzati
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The company name is required.',
        ];
    }
}