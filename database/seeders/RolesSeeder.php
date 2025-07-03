<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $basicRole = Role::create(['name' => 'panel_user']);

        $superAdmin = new User;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 's@f.mx';
        $superAdmin->password = bcrypt('s@f.mx');
        $superAdmin->save();

        $superAdmin->assignRole($superAdminRole);

        $basic = new User;
        $basic->name = 'User';
        $basic->email = 'u@f.mx';
        $basic->password = bcrypt('u@f.mx');
        $basic->save();

        $basic->assignRole($basicRole);
    }
}
