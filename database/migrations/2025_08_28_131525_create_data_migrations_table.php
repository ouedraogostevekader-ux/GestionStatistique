<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission as Permission;
use App\Models\User as User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $pu = Permission::create(['name' => 'permission-create', 'table' => 'Permission', 'groupe' => 'Administration']);
        $pd = Permission::create(['name' => 'permission-delete', 'table' => 'Permission', 'groupe' => 'Administration']);
        $pe = Permission::create(['name' => 'permission-edit', 'table' => 'Permission', 'groupe' => 'Administration']);
        $pl = Permission::create(['name' => 'permission-list', 'table' => 'Permission', 'groupe' => 'Administration']);
        $pm = Permission::create(['name' => 'managePermission', 'table' => 'Permission', 'groupe' => 'Administration']);

        $rc = Permission::create(['name' => 'role-create', 'table' => 'Role', 'groupe' => 'Administration']);
        $rd = Permission::create(['name' => 'role-delete', 'table' => 'Role', 'groupe' => 'Administration']);
        $re = Permission::create(['name' => 'role-edit', 'table' => 'Role', 'groupe' => 'Administration']);
        $rl = Permission::create(['name' => 'role-list', 'table' => 'Role', 'groupe' => 'Administration']);
        $rm = Permission::create(['name' => 'manageRole', 'table' => 'Role', 'groupe' => 'Administration']);

        $uc = Permission::create(['name' => 'user-create', 'table' => 'Utilisateur', 'groupe' => 'Administration']);
        $ud = Permission::create(['name' => 'user-delete', 'table' => 'Utilisateur', 'groupe' => 'Administration']);
        $ue = Permission::create(['name' => 'user-edit', 'table' => 'Utilisateur', 'groupe' => 'Administration']);
        $ul = Permission::create(['name' => 'user-list', 'table' => 'Utilisateur', 'groupe' => 'Administration']);
        $um = Permission::create(['name' => 'manageUser', 'table' => 'Utilisateur', 'groupe' => 'Administration']);

        /* Gestion des droits essentiels */

        $am = Permission::create(['name' => 'manageAdministration', 'table' => 'Admin', 'groupe' => 'Administration']);
        $am2 = Permission::create(['name' => 'manageAdmin', 'table' => 'InterfaceAdmin', 'groupe' => 'Administration']);
        $pam = Permission::create(['name' => 'manageParametre', 'table' => 'Paramétrage', 'groupe' => 'Paramétrage']);

        $log = Permission::create(['name' => 'log-list', 'table' => 'Log', 'groupe' => 'Administration']);
        $log2 = Permission::create(['name' => 'log-show', 'table' => 'Log', 'groupe' => 'Administration']);
        $log3 = Permission::create(['name' => 'manageLog', 'table' => 'Log', 'groupe' => 'Administration']);

        $role = Role::create(['name' => 'Admin']);
        $role->syncPermissions([$pu->name, $pd->name, $pe->name, $pl->name, $pm->name, $rc->name, $rd->name, $re->name, $rl->name, $rm->name, $uc->name, $ud->name, $ue->name, $ul->name, $um->name, $am->name, $am2->name, $pam->name, $log->name, $log2->name, $log3->name]);

        $user = User::create(['name' => 'admin', 'email' => 'admin@admin.bf', 'password' => Hash::make('password')]);

        $user->assignRole('Admin');
        $roleAdmin = Role::create(['name' => 'Super-Admin']);
        $user->assignRole('Super-Admin');
        // /* fin Gestion des droits essentiels */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('data_permissions');
    }
};
