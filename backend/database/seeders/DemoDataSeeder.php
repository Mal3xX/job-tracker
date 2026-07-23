<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\blogic\_Shared\Enums\ApplicationStatus;
use App\blogic\_Shared\Enums\CompanySize;
use App\blogic\_Shared\Enums\PlatformType;
use App\blogic\_Shared\Enums\WorkMode;
use App\blogic\Accounts\Models\Account;
use App\blogic\Applications\Models\Application;
use App\blogic\Companies\Models\Company;
use App\blogic\Companies\Models\Contact;
use App\blogic\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\blogic\_Shared\Services\SlugService;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);

        // ─── UTENTE ───
        $adminAccount = Account::where('email', 'test@prova.com')->first();

        if (!$adminAccount) {
            return;
        }

        $adminUser = User::where('account_id', $adminAccount->id)->first();

        if (!$adminUser) {
            return;
        }

        // ─── AZIENDE ───
        $companiesData = [
            ['name' => 'Dedagroup',     'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.dedagroup.it'],
            ['name' => 'Accenture',     'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.accenture.com'],
            ['name' => 'Reply',         'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.reply.com'],
            ['name' => 'Engineering',   'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.eng.it'],
            ['name' => 'Alten',         'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.alten.com'],
            ['name' => 'Var Group',     'sector' => 'Tech',       'size' => CompanySize::MEDIUM,  'website' => 'https://www.vargroup.it'],
            ['name' => 'Lutech',        'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.lutech.it'],
            ['name' => 'Fincons',       'sector' => 'Tech',       'size' => CompanySize::MEDIUM,  'website' => 'https://www.finconsgroup.com'],
            ['name' => 'NTT Data',      'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.nttdata.com'],
            ['name' => 'Capgemini',     'sector' => 'Tech',       'size' => CompanySize::LARGE,   'website' => 'https://www.capgemini.com'],
            ['name' => 'Avvale',        'sector' => 'Tech',       'size' => CompanySize::MEDIUM,  'website' => 'https://www.avvale.com'],
            ['name' => 'Deloitte',      'sector' => 'Finance',    'size' => CompanySize::LARGE,   'website' => 'https://www.deloitte.com'],
        ];

        $slugService = new SlugService();
        $companies = [];
        foreach ($companiesData as $data) {
            $companies[] = Company::create([
                'name' => $data['name'],
                'slug' => $slugService->generateUnique($data['name'], 'companies', 'slug'),
                'sector' => $data['sector'],
                'size' => $data['size']->value,
                'website' => $data['website'],
                'linkedin' => 'https://www.linkedin.com/company/' . strtolower(str_replace(' ', '', $data['name'])),
                'description' => fake()->paragraph(3),
                'logo_path' => null,
                'notes' => null,
                'creator_id' => $adminUser->id,
            ]);
        }

        // ─── CONTATTI ────
        $contactRoles = ['HR Manager', 'Talent Acquisition', 'IT Recruiter', 'Team Lead', 'CTO', 'Project Manager', 'Business Developer', 'HR Specialist'];
        foreach ($companies as $company) {
            $numContacts = random_int(1, 3);
            for ($i = 0; $i < $numContacts; $i++) {
                Contact::create([
                    'company_id' => $company->id,
                    'creator_id' => $adminUser->id,
                    'name' => fake()->name(),
                    'role' => $contactRoles[array_rand($contactRoles)],
                    'email' => fake()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'linkedin' => 'https://www.linkedin.com/in/' . fake()->userName(),
                    'is_principal' => $i === 0,
                    'notes' => $i === 0 ? 'Contatto principale' : null,
                ]);
            }
        }

        // ─── CANDIDATURE ───
        $jobTitles = [
            'Sviluppatore Frontend React',
            'Sviluppatore Backend Node.js',
            'Sviluppatore Fullstack PHP',
            'Cloud Engineer AWS',
            'DevOps Engineer',
            'Data Scientist',
            'Software Engineer Java',
            'Mobile Developer React Native',
            'QA Automation Engineer',
            'Cybersecurity Analyst',
            'Solution Architect',
            'Product Manager',
            'UX/UI Designer',
            'Sviluppatore .NET',
            'Sviluppatore Python',
            'AI Engineer',
            'Database Administrator',
            'Technical Project Manager',
            'System Administrator Linux',
            'Business Analyst IT',
        ];

        $locations = [
            'Milano',
            'Roma',
            'Torino',
            'Bologna',
            'Firenze',
            'Napoli',
            'Verona',
            'Padova',
            'Brescia',
            'Bergamo',
        ];

        $statuses = [
            ApplicationStatus::PENDING,
            ApplicationStatus::PENDING,
            ApplicationStatus::PENDING,
            ApplicationStatus::PENDING,
            ApplicationStatus::POSITIVE,
            ApplicationStatus::POSITIVE,
            ApplicationStatus::NEGATIVE,
            ApplicationStatus::NEGATIVE,
            ApplicationStatus::NEGATIVE,
            ApplicationStatus::NO_RESPONSE,
            ApplicationStatus::NO_RESPONSE,
            ApplicationStatus::INTERVIEW,
            ApplicationStatus::INTERVIEW,
            ApplicationStatus::INTERVIEW,
            ApplicationStatus::OFFER,
        ];

        $workModes = [WorkMode::REMOTE, WorkMode::HYBRID, WorkMode::HYBRID, WorkMode::OFFICE];
        $platforms = PlatformType::cases();
        $interviewStatuses = [ApplicationStatus::INTERVIEW, ApplicationStatus::OFFER, ApplicationStatus::POSITIVE];

        // 40 candidature normali
        for ($i = 0; $i < 40; $i++) {
            $status = $statuses[array_rand($statuses)];
            $createdAt = fake()->dateTimeBetween('-6 months', 'now');
            $company = $companies[array_rand($companies)];
            $salaryMin = fake()->numberBetween(25000, 45000);
            $salaryMax = $salaryMin + fake()->numberBetween(5000, 25000);
            $application = new Application([
                'company_id' => $company->id,
                'title' => $jobTitles[array_rand($jobTitles)],
                'work_mode' => $workModes[array_rand($workModes)]->value,
                'location' => $locations[array_rand($locations)],
                'link_job' => 'https://www.linkedin.com/jobs/view/' . fake()->randomNumber(8),
                'platform' => $platforms[array_rand($platforms)]->value,
                'status' => $status->value,
                'status_changed_at' => $createdAt,
                'interview_date' => in_array($status, $interviewStatuses) ? fake()->dateTimeBetween('-1 month', '+2 months') : null,
                'salary_min' => $salaryMin,
                'salary_max' => $salaryMax,
                'description' => fake()->paragraph(random_int(2, 5)),
                'notes' => fake()->optional(0.3)->paragraph(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $application->user_id = $adminUser->id;
            $application->save();
        }

        // ─── 5 CANDIDATURE PENDING (senza company_id) ───
        $pendingCompanies = [
            'Innovation Labs SRL',
            'Digital Solutions SpA',
            'NextGen Tech Startup',
            'CloudNative Italia',
            'SmartWork AI',
        ];
        for ($i = 0; $i < 5; $i++) {
            $createdAt = fake()->dateTimeBetween('-3 months', 'now');
            $status = $statuses[array_rand($statuses)];
            $application = new Application([
                'company_id' => null,
                'company_name' => $pendingCompanies[$i],
                'title' => $jobTitles[array_rand($jobTitles)],
                'work_mode' => $workModes[array_rand($workModes)]->value,
                'location' => $locations[array_rand($locations)],
                'platform' => $platforms[array_rand($platforms)]->value,
                'status' => $status->value,
                'status_changed_at' => $createdAt,
                'interview_date' => null,
                'salary_min' => fake()->numberBetween(28000, 40000),
                'salary_max' => fake()->numberBetween(42000, 60000),
                'description' => fake()->paragraph(random_int(2, 4)),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $application->user_id = $adminUser->id;
            $application->save();
        }
    }
}
