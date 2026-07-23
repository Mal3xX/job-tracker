<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest per la validazione dei dati di registrazione.
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a effettuare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per la registrazione.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:accounts,email',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ];
    }
}
