<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{

    function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:role-list', only: ['index', 'show']),
            new Middleware('permission:role-create', only: ['create', 'store']),
            new Middleware('permission:role-edit', only: ['edit', 'update']),
            new Middleware('permission:role-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permission = Permission::orderBy('table', 'ASC')->get()->groupBy('groupe');
        $rolePermissions = [];
        $rolePermis = [];
        foreach ($permission as $key => $values) {
            $values = $values->groupBy('table');

            $rolePermis[$key] = $values;
        }
        return view('roles.create', compact('rolePermis', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            //'permission' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $permission = Permission::orderBy('table', 'ASC')->get()->groupBy('groupe');
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        $rolePermis = [];
        foreach ($permission as $key => $values) {
            $values = $values->groupBy('table');
            $rolePermis[$key] = $values;
        }

        return view('roles.show', compact('role', 'rolePermissions', 'permission', 'rolePermis'));
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::orderBy('table', 'ASC')->get()->groupBy('groupe');
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        $rolePermis = [];
        foreach ($permission as $key => $values) {
            $values = $values->groupBy('table');
            $rolePermis[$key] = $values;
        }
        return view('roles.edit', compact('role', 'permission', 'rolePermissions', 'rolePermis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
