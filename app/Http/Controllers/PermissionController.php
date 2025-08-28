<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission as Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller //implements HasMiddleware
{
    //     function __construct() {}

    //     public static function middleware(): array
    //     {
    //         return [
    //             new Middleware('permission:permission-list', only: ['index', 'show']),
    //             new Middleware('permission:permission-create', only: ['create', 'store']),
    //             new Middleware('permission:permission-edit', only: ['edit', 'update']),
    //             new Middleware('permission:permission-delete', only: ['destroy']),
    //         ];
    //     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions = Permission::orderBy('ulid', 'DESC')->get();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groupes['Administration'] = 'Administration';
        $groupes['Paramétrage'] = 'Paramétrage';
        $groupes['Gestion'] = 'Gestion';
        return view('permissions.create', compact('groupes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show($permission)
    {
        $permission = Permission::find($permission);
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit($permission)
    {
        $groupes['Administration'] = 'Administration';
        $groupes['Paramétrage'] = 'Paramétrage';
        $groupes['Gestion'] = 'Gestion';
        $permission = Permission::find($permission);
        return view('permissions.edit', compact('permission', 'groupes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy($permission)
    {
        Permission::find($permission)->delete();
        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
