<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use App\Models\User;
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
        // Generate all permissions
        foreach (PermissionEnum::cases() as $permissionEnum) {
            Permission::firstOrCreate(['name' => $permissionEnum->value]);
        }

        // Generate all roles and assign corresponding permissions
        foreach (RoleEnum::cases() as $roleEnum) {
            $role = Role::firstOrCreate(
                ['slug' => $roleEnum->value],
                ['name' => ucfirst($roleEnum->value)]
            );
            $permissions = array_map(
                fn($p) => $p->value,
                $roleEnum->permissions()
            );
            $role->syncPermissions($permissions);
        }

        // Create one user per role
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        $cashier = User::factory()->create([
            'name' => 'Cashier User',
            'username' => 'cashier',
            'email' => 'cashier@example.com',
            'email_verified_at' => now(),
        ]);
        $cashier->assignRole(RoleEnum::CASHIER->value);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'username' => 'manager',
            'email' => 'manager@example.com',
            'email_verified_at' => now(),
        ]);
        $manager->assignRole(RoleEnum::MANAGER->value);

        // Tambahan dummy users
        User::factory()->count(10)->create();

        $this->call([
            ItemSeeder::class,
            SaleSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
