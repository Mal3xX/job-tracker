<?php

declare(strict_types=1);

namespace Database\Factories;

use App\blogic\Accounts\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory per la creazione di istanze di account.
 * Genera dati fittizzi per i test e il seeding del database.
 */
class AccountFactory extends Factory
{
    /**
     * La password corrente utilizzata dalla factory.
     */
    protected static ?string $password;

    /**
     * Ruolo desiderato da applicare dopo la creazione.
     */
    protected string $desiredRole = 'user';

    /**
     * Configura la factory per applicare il ruolo dopo la creazione.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Account $account): void {
            if ($account->role !== $this->desiredRole) {
                $account->role = $this->desiredRole;
                $account->save();
            }
        });
    }

    /**
     * Definisce lo stato predefinito del modello.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= 'password',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica che l'email dell'account non è verificata.
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indica che l'account è un amministratore.
     * @return static
     */
    public function admin(): static
    {
        $this->desiredRole = 'admin';
        return $this->state(fn (array $attributes) => []);
    }
}