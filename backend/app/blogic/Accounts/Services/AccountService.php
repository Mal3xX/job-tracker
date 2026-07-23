<?php

declare(strict_types=1);

namespace App\blogic\Accounts\Services;

use App\blogic\Accounts\Models\Account;
use App\blogic\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Service per la logica di autenticazione.
 * Gestisce login, registrazione, logout e recupero dati utente.
 */
class AccountService
{
    /**
     * Autentica l'utente e restituisce i dati senza token.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        if (!Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            throw new RuntimeException('Invalid credentials');
        }

        $account = Auth::guard('web')->user();
        $token = $account->createToken('spa')->plainTextToken;

        /**
         * Registra l'accesso nello storico login.
         */
        DB::table('login_histories')->insert([
            'user_id'    => $account->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'account' => [
                'id' => $account->id,
                'email' => $account->email,
                'role' => $account->role,
                'email_verified_at' => $account->email_verified_at,
            ],
            'user' => [
                'id' => $account->user?->id,
                'first_name' => $account->user?->first_name,
                'last_name' => $account->user?->last_name,
            ],
            'token' => $token,
        ];
    }

    /**
     * Registra un nuovo account e utente in transazione.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $account = Account::create([
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
            $account->role = 'user';
            $account->save();

            $user = User::create([
                'account_id' => $account->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]);

            Auth::guard('web')->login($account);

            return [
                'account' => [
                    'id' => $account->id,
                    'email' => $account->email,
                    'role' => $account->role,
                    'email_verified_at' => $account->email_verified_at,
                ],
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ],
            ];
        });
    }

    /**
     * Esegue il logout della sessione web corrente.
     *
     * @param Account $account
     */
    public function logout(Account $account): void
    {
        Auth::guard('web')->logout();
    }

    /**
     * Restituisce i dati dell'account autenticato.
     *
     * @param Account $account
     * @return array
     */
    public function me(Account $account): array
    {
        $account->load('user');

        return [
            'account' => [
                'id' => $account->id,
                'email' => $account->email,
                'role' => $account->role,
                'email_verified_at' => $account->email_verified_at,
            ],
            'user' => [
                'id' => $account->user?->id,
                'first_name' => $account->user?->first_name,
                'last_name' => $account->user?->last_name,
                'avatar_path' => $account->user?->avatar_path,
            ],

        ];
    }

    /**
     * Aggiorna il profilo utente: email, password e avatar.
     */
    public function updateProfile(Account $account, array $data): array
    {
        if (isset($data['email']) && $data['email'] !== $account->email) {
            $account->email = $data['email'];
            $account->save();
        }
        if (!empty($data['password'])) {
            $account->password = $data['password'];
            $account->save();
        }
        $user = $account->user;
        if (isset($data['avatar'])) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $data['avatar']->store('avatars', 'public');
            $user->avatar_path = $path;
            $user->save();
        } elseif (!empty($data['remove_avatar'])) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
            $user->save();
        }
        $account->load('user');
        return [
            'account' => [
                'id' => $account->id,
                'email' => $account->email,
                'role' => $account->role,
                'email_verified_at' => $account->email_verified_at,
            ],
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'avatar_path' => $user->avatar_path,
            ],
        ];
    }
}
