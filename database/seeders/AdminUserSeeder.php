<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se esiste già un admin
        $adminExists = User::where('is_admin', true)->exists();

        if (!$adminExists) {
            // Crea l'admin principale
            User::create([
                'name' => 'Admin',
                'email' => 'admin@portfolio.test', // Cambia con la tua email
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // CAMBIA QUESTA PASSWORD!
                'is_admin' => true,
            ]);

            $this->command->info('✅ Utente admin creato con successo!');
            $this->command->warn('⚠️  IMPORTANTE: Cambia la password di default!');
            $this->command->info('Email: admin@portfolio.test');
            $this->command->info('Password: password');
        } else {
            $this->command->info('Un utente admin esiste già.');
        }
    }
}
