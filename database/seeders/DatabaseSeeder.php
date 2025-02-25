<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $viewerRole = Role::create(['name' => 'viewer']);

        $permissions = ['create user', 'edit user', 'delete user', 'view user'];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole->givePermissionTo(Permission::all());
        $editorRole->givePermissionTo(['edit user']);
        $viewerRole->givePermissionTo(['view user']);

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123')
        ]);
        $admin->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'editor',
            'email' => 'editor@gmail.com',
            'password' => bcrypt('20042004')
        ]);
        $user->assignRole('editor');

        $user = User::factory()->create([
            'name' => 'viewer',
            'email' => 'viewer@gmail.com',
            'password' => bcrypt('20042004')
        ]);
        $user->assignRole('viewer');
    }
}
