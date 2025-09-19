<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            'view-candidats', 'create-candidats', 'classify-candidats', 'migrate-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces', 'delete-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes',
            'manage-contrats', 'manage-entretiens',
            'view-profile', 'update-profile'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view-candidats', 'create-candidats', 'classify-candidats', 'migrate-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces', 'delete-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes',
            'view-profile'
        ]);

        $employeRole = Role::create(['name' => 'employe']);
        $employeRole->givePermissionTo(['view-profile', 'update-profile']);
    }
}