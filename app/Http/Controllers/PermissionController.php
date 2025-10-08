<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission as Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:permission-list', only: ['index', 'show']),
            new Middleware('permission:permission-create', only: ['create', 'store']),
            new Middleware('permission:permission-edit', only: ['edit', 'update']),
            new Middleware('permission:permission-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $permissions = Permission::orderBy('ulid', 'DESC')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        $groupes['Administration'] = 'Administration';
        $groupes['Paramétrage'] = 'Paramétrage';
        $groupes['Gestion'] = 'Programme';
        $groupes['Tableau'] = 'Tableau';
        $groupes['PIP'] = 'PIP';
        return view('permissions.create', compact('groupes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $input = $request->all();
        $permission = Permission::create($input);
        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully');
    }

    public function show($permission)
    {
        $permission = Permission::find($permission);
        return view('permissions.show', compact('permission'));
    }

    public function edit($permission)
    {
        $groupes['Administration'] = 'Administration';
        $groupes['Paramétrage'] = 'Paramétrage';
        $groupes['Gestion'] = 'Programme';
        $groupes['Tableau'] = 'Tableau';
        $groupes['PIP'] = 'PIP';
        $permission = Permission::find($permission);
        return view('permissions.edit', compact('permission', 'groupes'));
    }

    public function update(Request $request, $permission)
    {
        $permission = Permission::find($permission);
        $request->validate([
            'name' => 'required',
        ]);

        $input = $request->all();
        $permission->update($input);
        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy($permission)
    {
        Permission::find($permission)->delete();
        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
