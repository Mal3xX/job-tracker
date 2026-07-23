<?php

declare(strict_types=1);

namespace Database\Factories;

use App\blogic\Accounts\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per la creazione di istanze di User.
 * Genera dati fittizzi per i test e il seeding del database.
 */
class UserFactory extends Factory
{
    /**
     * Definisce lo stato predefinito del modello.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];
    }
}