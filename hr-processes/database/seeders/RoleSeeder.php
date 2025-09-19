<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Créer les rôles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $employeRole = Role::create(['name' => 'employe']);

        // Créer les permissions
        $permissions = [
            'view-candidats', 'create-candidats', 'edit-candidats', 'delete-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces', 'delete-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes', 'edit-employes',
            'manage-contrats', 'manage-entretiens',
            'view-profile'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assigner les permissions aux rôles
        $adminRole->givePermissionTo(Permission::all());
        $managerRole->givePermissionTo([
            'view-candidats', 'create-candidats', 'edit-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes',
            'manage-contrats', 'manage-entretiens',
            'view-profile'
        ]);
        $employeRole->givePermissionTo('view-profile');
    }
}