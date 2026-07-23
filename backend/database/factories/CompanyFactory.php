<?php

declare(strict_types=1);

namespace Database\Factories;

use App\blogic\_Shared\Enums\CompanySize;
use App\blogic\_Shared\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per la creazione di istanze di Company.
 * Genera dati fittizzi per i test e il seeding del database.
 */
class CompanyFactory extends Factory
{
    /**
     * Definisce lo stato predefinito del modello.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => (new SlugService())->generateUnique($name, 'companies', 'slug'),
            'sector' => fake()->randomElement(['Tech', 'Finance', 'Healthcare', 'Retail', 'Manufacturing', 'Education']),
            'size' => fake()->randomElement(CompanySize::cases())->value,
            'website' => fake()->optional()->url(),
            'linkedin' => fake()->optional()->url(),
            'description' => fake()->optional()->paragraph(),
            'logo_path' => null,
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}