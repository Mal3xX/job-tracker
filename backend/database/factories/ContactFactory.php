<?php

declare(strict_types=1);

namespace Database\Factories;

use App\blogic\Companies\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per la creazione di istanze i Contact.
 * Genera dati fittizzi per i test e il seeding del database
 */
class ContactFactory extends Factory
{
    /**
     * Definisce lo stato predefinito del modello.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'role' => fake()->optional()->jobTitle(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'linkedin' => fake()->optional()->url(),
            'is_principal' => fake()->boolean(30),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}