<?php

declare(strict_types=1);

namespace App\blogic\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validazione per la creazione di un nuovo contatto.
 */
class StoreContactRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a fare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per la creazione di un contatto.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string|max:500',
            'is_principal' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ];
    }
}