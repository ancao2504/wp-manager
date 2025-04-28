<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions if they don't exist
        $permissions = [
            // Sites permissions
            'view sites',
            'create sites',
            'edit sites',
            'delete sites',
            
            // Posts permissions
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'sync posts',
            
            // Products permissions
            'view products',
            'create products',
            'edit products',
            'delete products',
            'import products',
            'export products',
            
            // SEO permissions
            'view seo',
            'analyze seo',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles'
        ];
        
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles if they don't exist and assign permissions
        
        // Admin role - has all permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->syncPermissions(Permission::all());

        // Editor role - content management but not user/role management
        $editorRole = Role::findOrCreate('editor');
        $editorRole->syncPermissions([
            'view sites', 'edit sites',
            'view posts', 'create posts', 'edit posts', 'delete posts', 'sync posts',
            'view products', 'create products', 'edit products', 'delete products', 'import products', 'export products',
            'view seo', 'analyze seo'
        ]);

        // Viewer role - read-only permissions
        $viewerRole = Role::findOrCreate('viewer');
        $viewerRole->syncPermissions([
            'view sites',
            'view posts',
            'view products',
            'view seo'
        ]);
    }
}
