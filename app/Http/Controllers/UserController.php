<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB as DB;

use Spatie\Permission\Models\Role as ModelsRole;

class UserController extends Controller implements HasMiddleware
{
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:user-list', only: ['index', 'show']),
            new Middleware('permission:user-create', only: ['create', 'store']),
            new Middleware('permission:user-edit', only: ['edit', 'update']),
            new Middleware('permission:user-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $users = User::all();
        return view('users.index')
            ->with('users', $users);
    }

    public function create()
    {
        $roles = Role::all();
        $structures = Structure::all();
        $userRole = [];
        $user = null;
        return view('users.create', compact('roles', 'userRole', 'user', 'structures'));
    }

    /**
     * Store a newly created User in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'email' =>  ['required', Rule::unique('users', 'email')->whereNull('deleted_at')],
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    //'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    //'regex:/[0-9]/',      // must contain at least one digit
                    //'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'password_confirm' => 'same:password',
                'roles' => 'required',
                'confirm-password' => 'required|same:password',
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'fonction' => 'required|string',
                'matricule' => ['required', 'string', Rule::unique('users', 'matricule')->whereNull('deleted_at')],
                'telephone' => 'required|string',
                'structure_id' => 'required|string',
                // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ],
            [
                'nom.*' => 'Le nom est obligatoire',
                'prenom.*' => 'Le prénom est obligatoire',
                'matricule.required' => 'Le matricule est obligatoire',
                'matricule.unique' => 'Ce matricule est déjà utilisé',
                'telephone.*' => 'Le telephone est obligatoire',
                'password.required' => 'Le mot de passe est obligatoire',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères',
                'password.same' => 'Les saisies ne sont pas conformes',
                'password.regex' => 'Le mot de passe doit contenir au moins une lettre',
                'confirm-password.required' => 'Veuillez confirmer le mot de passe',
                // 'photo.required' => 'Veuillez sélectionner une image',
                // 'photo.image' => 'Le fichier doit être une image',
                // 'photo.mimes' => 'Formats acceptés: jpeg, png, jpg, gif',
                // 'photo.max' => 'La taille maximale est de 2MB'
            ]
        );

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        DB::beginTransaction();
        try {
            $user =  User::create($input);
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('photos-profile', 'public');
                $user->update(['photo' => $path]);
            }
            $user->assignRole($request->input('roles'));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index')->with('error', 'Erreur lors de la création de User');
        }

        return redirect()->route('users.index')->with('success', 'User créé avec succès');
    }

    /**
     * Display the specified User.
     */
    public function show($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     */
    public function edit($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }

        $structures = Structure::all();
        $roles = Role::all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('users.edit', compact('user', 'roles', 'userRole', 'structures'));
    }

    /**
     * Show the form for editing the specified User.
     */
    public function profile($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }
        $structures = Structure::all();
        return view('users.profile', compact('user', 'structures'));
    }

    /**
     * Show the form for editing the specified User.
     */
    public function changepwd($id)
    {
        if ($id != Auth::id()) {
            abort(403);
        }
        $user = User::find($id);
        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }

        return view('users.changepwd', compact('user', 'communes'));
    }

    /**
     * Update the specified User in storage.
     */
    public function changepwdstore($id, Request $request)
    {
        if ($id != Auth::id()) {
            abort(403);
        }
        $user = User::find($id);

        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }
        $input = $request->all();

        $request->validate(
            [
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-zA-Z]/',      // must contain at least one lowercase letter
                    //'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    //'regex:/[0-9]/',      // must contain at least one digit
                    //'regex:/[@$!%*#?&]/', // must contain a special character
                    'same:confirm-password',
                ],
                'confirm-password' => 'required|same:password',
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'fonction' => 'required|string',
                'matricule' => ['required', 'string', Rule::unique('users', 'matricule')->ignore($id)->whereNull('deleted_at')],
                'telephone' => 'required|string',
                'commune_id' => 'required|string',
                'email' => ['required', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ],
            [
                'nom.*' => 'Le nom est obligatoire',
                'prenom.*' => 'Le prénom est obligatoire',
                'matricule.required' => 'Le matricule est obligatoire',
                'matricule.unique' => 'Ce matricule est déjà utilisé',
                'telephone.*' => 'Le telephone est obligatoire',
                'password.required' => 'Le mot de passe est obligatoire',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères',
                'password.same' => 'Les saisies ne sont pas conformes',
                'password.regex' => 'Le mot de passe doit contenir au moins une lettre',
                'confirm-password.required' => 'Veuillez confirmer le mot de passe',
                'photo.required' => 'Veuillez sélectionner une image',
                'photo.image' => 'Le fichier doit être une image',
                'photo.mimes' => 'Formats acceptés: jpeg, png, jpg, gif',
                'photo.max' => 'La taille maximale est de 2MB'
            ]
        );

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('photos-profile', 'public');
            $user->update(['photo' => $path]);
        }

        $input['password'] = Hash::make($input['password']);
        $user->update($input);

        return redirect()->route('users.index')->with('success', 'Mot de passe modifié avec success');
    }

    /**
     * Update the specified User in storage.
     */
    public function update($id, Request $request)
    {
        $user = User::find($id);

        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }

        $request->validate(
            [
                'nom' => 'required|string',
                'prenom' => 'required|string',
                'fonction' => 'required|string',
                'matricule' => ['required', 'string', Rule::unique('users', 'matricule')->ignore($id)->whereNull('deleted_at')],
                'telephone' => 'required|string',
                'email' => ['required', Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at')],
                'password' => 'same:confirm-password',
                'confirm-password' => 'same:password',
                'structure_id' => 'required|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ],
            [
                'nom.*' => 'Le nom est obligatoire',
                'prenom.*' => 'Le prénom est obligatoire',
                'matricule.required' => 'Le matricule est obligatoire',
                'matricule.unique' => 'Ce matricule est déjà utilisé',
                'telephone.*' => 'Le telephone est obligatoire',
                'password.required' => 'Le mot de passe est obligatoire',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères',
                'password.same' => 'Les saisies ne sont pas conformes',
                'password.regex' => 'Le mot de passe doit contenir au moins une lettre',
                'confirm-password.required' => 'Veuillez confirmer le mot de passe',
                // 'photo.required' => 'Veuillez sélectionner une image',
                // 'photo.image' => 'Le fichier doit être une image',
                // 'photo.mimes' => 'Formats acceptés: jpeg, png, jpg, gif',
                // 'photo.max' => 'La taille maximale est de 2MB'
            ]
        );

        $input = $request->all();

        if (!empty($input['password'])) {
            $request->validate([

                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    //'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    //'regex:/[0-9]/',      // must contain at least one digit
                    //'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'confirm-password' => 'required|same:password',
            ]);
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }
        $user->update($input);
        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('photos-profile', 'public');
            $user->update(['photo' => $path]);
        }


        if (isset($input['roles'])) {
            $user->syncRoles($request->input('roles'));
        } else {
            return redirect()->route('dashboard')->with('success', 'User modifié avec success');
        }

        return redirect()->route('users.index')->with('success', 'User modifié avec success');
    }

    /**
     * Remove the specified User from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'User non retrouvé');
        }
        $user->delete();
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        return redirect()->route('users.index')->with('success', 'User supprimé avec success');
    }
}
