<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $rolePanelUser = Role::findByName('panel_user');

        // Permisos
        $permissionViewAnyRevenue = Permission::findByName('view_any_revenue');
        $permissionViewRevenue = Permission::findByName('view_revenue');
        $permissionCreateRevenue = Permission::findByName('create_revenue');
        $permissionUpdateRevenue = Permission::findByName('update_revenue');
        $permissionDeleteRevenue = Permission::findByName('delete_revenue');

        $permissionViewAnyCategory = Permission::findByName('view_any_category');
        $permissionViewCategory = Permission::findByName('view_category');
        $permissionCreateCategory = Permission::findByName('create_category');
        $permissionUpdateCategory = Permission::findByName('update_category');
        $permissionDeleteCategory = Permission::findByName('delete_category');

        $permissionViewAnyBill = Permission::findByName('view_any_bill');
        $permissionViewBill = Permission::findByName('view_bill');
        $permissionCreateBill = Permission::findByName('create_bill');
        $permissionUpdateBill = Permission::findByName('update_bill');
        $permissionDeleteBill = Permission::findByName('delete_bill');

        // Permisos Panel User

        $rolePanelUser->givePermissionTo($permissionViewAnyRevenue);
        $rolePanelUser->givePermissionTo($permissionViewRevenue);
        $rolePanelUser->givePermissionTo($permissionCreateRevenue);
        $rolePanelUser->givePermissionTo($permissionUpdateRevenue);
        $rolePanelUser->givePermissionTo($permissionDeleteRevenue);

        $rolePanelUser->givePermissionTo($permissionViewAnyCategory);
        $rolePanelUser->givePermissionTo($permissionViewCategory);
        $rolePanelUser->givePermissionTo($permissionCreateCategory);
        $rolePanelUser->givePermissionTo($permissionUpdateCategory);
        $rolePanelUser->givePermissionTo($permissionDeleteCategory);

        $rolePanelUser->givePermissionTo($permissionViewAnyBill);
        $rolePanelUser->givePermissionTo($permissionViewBill);
        $rolePanelUser->givePermissionTo($permissionCreateBill);
        $rolePanelUser->givePermissionTo($permissionUpdateBill);
        $rolePanelUser->givePermissionTo($permissionDeleteBill);

    }
}

// view_revenue
