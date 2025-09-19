<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-candidats', 'create-candidats', 'classify-candidats', 'migrate-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces', 'delete-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes',
            'manage-contrats', 'manage-entretiens',
            'view-profile', 'update-profile'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        $roles = ['admin', 'manager', 'employe'];
        $roleObjects = [];
        foreach ($roles as $roleName) {
            $roleObjects[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        $roleObjects['admin']->syncPermissions(Permission::all());

        $roleObjects['manager']->syncPermissions([
            'view-candidats', 'create-candidats', 'classify-candidats', 'migrate-candidats',
            'view-annonces', 'create-annonces', 'edit-annonces', 'delete-annonces',
            'view-candidatures', 'manage-selections',
            'view-employes', 'create-employes',
            'view-profile'
        ]);

        $roleObjects['employe']->syncPermissions(['view-profile', 'update-profile']);
    }
}