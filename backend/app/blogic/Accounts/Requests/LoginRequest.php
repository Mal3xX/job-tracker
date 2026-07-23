<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest per la validazione delle credenziali di login.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a effettuare questa richiesta.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per il login.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }
}
