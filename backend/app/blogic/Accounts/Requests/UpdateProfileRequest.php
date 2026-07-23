<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validazione per l'aggiornamento del profilo utente.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'sometimes|email|unique:accounts,email,' . $this->user()->id,
            'password' => 'sometimes|nullable|min:8|confirmed',
            'avatar' => 'sometimes|image|max:2048',
            'remove_avatar' => 'sometimes|boolean',
        ];
    }
}
