<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Users\Enums\RolesEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::query()->truncate();
        Permission::query()->truncate();

        $usersPermissions = [
            'leave_comment',
            'view_product',
        ];

        $permissions = [
            ...$usersPermissions,
        ];

        foreach ($permissions as $permission) {
            Permission::query()->create(['name' => $permission, 'guard_name' => 'web']);
        }

        $managerPermissions = [
            'manage_categories',
            'manage_brands',
            'manage_product',
            'publish_product',
            'trash_product',
            'delete_product',
            'manage_contents',
            'show_users',
            'show_categories',
            'show_brands',
            'show_products',
            'show_orders',
            'show_content_management',
            'show_warehouse',
            'manage_warehouse',
            'manage_orders',
            'coupons_view',
            'coupons_manage',
        ];

        $superManagerPermissions = [
            'create_manager',
            'delete_manager',
            'update_manager',
        ];

        $managersPermissions = [
            ...$superManagerPermissions,
            ...$managerPermissions,
        ];

        foreach ($managersPermissions as $permission) {
            Permission::query()->create(['name' => $permission, 'guard_name' => RolesEnum::MANAGER->toString()]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $regularUserRole = Role::create(['name' => 'regular_user']);
        $regularUserRole->givePermissionTo(['view_product', ...$usersPermissions]);

        $guestUser = Role::create(['name' => 'guest']);
        $guestUser->givePermissionTo($guestUser);

        $manager = Role::create(['name' => 'manager', 'guard_name' => RolesEnum::MANAGER->toString()]);
        $manager->givePermissionTo($managerPermissions);
    }
}
