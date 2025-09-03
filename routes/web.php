<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgrammeBudgetaireController;

Route::get('/', function () {
    return view('layouts.acceuilLayout');
});

Route::get('/dashboard', function () {
    return view('layouts.admin');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::resource('programmebudgetaire', ProgrammeBudgetaireController::class);

});


require __DIR__ . '/auth.php';
