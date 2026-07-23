<?php

declare(strict_types=1);

namespace App\Providers;

use App\blogic\Accounts\Models\Account;
use App\blogic\Admin\Policies\AdminPolicy;
use App\blogic\Applications\Models\Application;
use App\blogic\Applications\Policies\ApplicationPolicy;
use App\blogic\Companies\Models\Company;
use App\blogic\Companies\Models\Contact;
use App\blogic\Companies\Policies\CompanyPolicy;
use App\blogic\Companies\Policies\ContactPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Account::class, AdminPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
    }
}
