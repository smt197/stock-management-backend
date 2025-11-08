<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer tous les utilisateurs existants (optionnel)
        // User::truncate();

        $users = [
            [
                'name' => 'Administrateur',
                'email' => 'admin@stock.com',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Gestionnaire',
                'email' => 'manager@stock.com',
                'password' => Hash::make('Manager@123'),
                'role' => 'manager',
            ],
            [
                'name' => 'Utilisateur',
                'email' => 'user@stock.com',
                'password' => Hash::make('User@123'),
                'role' => 'user',
            ],
            [
                'name' => 'Observateur',
                'email' => 'viewer@stock.com',
                'password' => Hash::make('Viewer@123'),
                'role' => 'viewer',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Critère de recherche
                $userData // Données à créer/mettre à jour
            );
        }

        $this->command->info('✅ 4 utilisateurs créés avec succès!');
        $this->command->info('');
        $this->command->info('📋 Identifiants de connexion:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👑 Admin      : admin@stock.com    / Admin@123');
        $this->command->info('🔧 Manager    : manager@stock.com  / Manager@123');
        $this->command->info('👤 User       : user@stock.com     / User@123');
        $this->command->info('👁️  Viewer     : viewer@stock.com   / Viewer@123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
