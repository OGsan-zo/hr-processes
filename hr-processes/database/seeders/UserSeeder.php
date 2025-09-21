<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Rôles (s'assurer qu'ils existent)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $employeRole = Role::firstOrCreate(['name' => 'employe']);

        // Utilisateurs de test
        $users = [
            [
                'name' => 'Admin RH',
                'email' => 'admin@rh.com',
                'password' => Hash::make('admin123'),
                'role' => $adminRole
            ],
            [
                'name' => 'Manager Recrutement',
                'email' => 'manager@rh.com',
                'password' => Hash::make('manager123'),
                'role' => $managerRole
            ],
            [
                'name' => 'Employé Test',
                'email' => 'employe@rh.com',
                'password' => Hash::make('employe123'),
                'role' => $employeRole
            ],
            [
                'name' => 'Test Utilisateur',
                'email' => 'test@rh.com',
                'password' => Hash::make('password123'),
                'role' => $managerRole
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );
            $user->assignRole($userData['role']);
        }

        $this->command->info('✅ 4 utilisateurs créés avec rôles assignés !');
        $this->command->table(
            ['Nom', 'Email', 'Mot de passe', 'Rôle'],
            array_map(function ($userData) {
                return [
                    $userData['name'],
                    $userData['email'],
                    $userData['password'],
                    $userData['role']->name
                ];
            }, $users)
        );
    }
}