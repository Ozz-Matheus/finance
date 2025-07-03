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
        $superAdmin->name = 'Administrador';
        $superAdmin->email = 'montesinos.quintana@gmail.com';
        $superAdmin->password = bcrypt('montesinos.quintana@gmail.com');
        $superAdmin->save();

        $superAdmin->assignRole($superAdminRole);

        $basic = new User;
        $basic->name = 'Usuario';
        $basic->email = 'dg.patriciavm@gmail.com';
        $basic->password = bcrypt('dg.patriciavm@gmail.com');
        $basic->save();

        $basic->assignRole($basicRole);
    }
}
