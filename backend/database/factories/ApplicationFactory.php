<?php

declare(strict_types=1);

namespace Database\Factories;

use App\blogic\_Shared\Enums\ApplicationStatus;
use App\blogic\_Shared\Enums\PlatformType;
use App\blogic\_Shared\Enums\WorkMode;
use App\blogic\Applications\Models\Application;
use App\blogic\Companies\Models\Company;
use App\blogic\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory per la creazione di istanze di Application.
 * Genera dati fittizzi per i test e il seeding del database.
 */
class ApplicationFactory extends Factory
{
    /**
     * Configura la factory per assegnare l'utente dopo la creazione.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Application $application): void {
            if ($application->user_id === null) {
                $application->user_id = User::factory()->create()->id;
                $application->save();
            }
        });
    }

    /**
     * Definisce lo stato predefinito del modello.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(ApplicationStatus::cases());
        $interviewStatuses = [
            ApplicationStatus::INTERVIEW,
            ApplicationStatus::OFFER,
            ApplicationStatus::POSITIVE,
        ];

        return [
            'company_id' => Company::factory(),
            'title' => fake()->jobTitle(),
            'work_mode' => fake()->randomElement(WorkMode::cases())->value,
            'location' => fake()->city() . ', ' . fake()->country(),
            'link_job' => fake()->optional()->url(),
            'platform' => fake()->randomElement(PlatformType::cases())->value,
            'status' => $status->value,
            'status_changed_at' => now(),
            'interview_date' => in_array($status, $interviewStatuses, true)
                ? fake()->dateTimeBetween('now', '+2 months')
                : null,
            'salary_min' => fake()->optional()->numberBetween(25000, 40000),
            'salary_max' => fake()->optional()->numberBetween(40001, 80000),
            'description' => fake()->optional()->paragraph(),
            'notes' => fake()->optional()->paragraph(),
        ]; 
    }
}