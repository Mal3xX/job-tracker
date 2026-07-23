<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\blogic\Accounts\Models\Account;
use App\blogic\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $account = Account::create([
            'email' => 'test@prova.com',
            'password' => 'Password123',
        ]);
        $account->role = 'admin';
        $account->save();
        
        User::create([
            'account_id' => $account->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);
    }
}