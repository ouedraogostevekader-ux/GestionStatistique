<?php

use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StructureController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgrammeBudgetaireController;

Route::get('/', function () {
    return view('layouts.acceuilLayout');
});

/* Route::get('/dashboard', function () {
    return view('layouts.admin');
})->middleware(['auth', 'verified'])->name('dashboard');
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('users/{id}/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
    Route::get('users/{id}/changepwd', [App\Http\Controllers\UserController::class, 'changepwd'])->name('users.changepwd');
    Route::patch('users/{id}/changepwd', [App\Http\Controllers\UserController::class, 'changepwdstore'])->name('users.changepwdstore');

    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('logs', [App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    Route::post('logs', [App\Http\Controllers\LogController::class, 'index'])->name('logs.indexp');
    Route::get('logs/show', [App\Http\Controllers\LogController::class, 'show'])->name('logs.show');
    Route::get('logs/{id}/causedBy', [App\Http\Controllers\LogController::class, 'causedBy'])->name('logs.causedBy');
    Route::get('logs/{id}/{model}/subjectOn', [App\Http\Controllers\LogController::class, 'subjectOn'])->name('logs.subjectOn');

    // / Groupe de routes pour les structures avec middleware admin
    Route::prefix('admin')->group(function () {
        // Page principale du tableau de bord
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/statistiques', [DashboardController::class, 'statistique'])->name('admin.statistique');
        // Routes CRUD complètes pour les structures
        Route::resource('structures', StructureController::class);

        // Routes supplémentaires pour les fonctionnalités avancées
        Route::get('structures/search', [StructureController::class, 'search'])->name('structures.search');
        Route::get('structures/filter/type', [StructureController::class, 'filterByType'])->name('structures.filter.type');

    });

    // Activites
    Route::resource('activites', ActiviteController::class);
    Route::resource('programmebudgetaire', ProgrammeBudgetaireController::class);
    route::resource('objectifStrategiques', App\Http\Controllers\ObjectifStrategiqueController::class);
    Route::get('suivis/create/{id}', [App\Http\Controllers\SuiviController::class, 'createByMIndicateur'])->name('suivis.createM');
    route::resource('suivis', App\Http\Controllers\SuiviController::class);
    Route::resource('actions', App\Http\Controllers\ActionController::class);
    Route::resource('objectifs-specifiques', App\Http\Controllers\ObjectifSpecifiqueController::class);
    Route::resource('indicateurs-programmes', App\Http\Controllers\IndicateurProgrammeController::class);
});


require __DIR__ . '/auth.php';
