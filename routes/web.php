<?php

use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\AnnuaireController;
use App\Http\Controllers\AssignationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ObjectifStrategiqueController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\StructureController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgrammeBudgetaireController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProgrammeActiviteController;
use App\Http\Controllers\LigneTableauController;
use App\Http\Controllers\ColonneTableauController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\Pip\BailleurController;
use App\Http\Controllers\Pip\ProgrammeController;
use App\Http\Controllers\Pip\ProjetController;
use App\Http\Controllers\Pip\SecteurController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\VersionController;

// Route::get('/', function () {
//     return view('layouts.acceuilLayout');
// })->name('acceuil');

Route::get('/', function () {
    return redirect()->route('login');
})->name('acceuil');

Route::get('/user/{id}/confirm-compte/', [UserController::class, 'edit_perso'])->name('edit.compte');
Route::patch('/user/{id}/confirm-compte/enregistre', [UserController::class, 'update_perso'])->name('update.compte');

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

    //Logs, Utilisateurs, Roles et Permissions
    Route::resource('users', App\Http\Controllers\UserController::class); //->except(['update', 'edit']);
    Route::patch('/users/{id}/deactivate', [App\Http\Controllers\UserController::class, 'deactivate'])->name('users.deactivate');
    Route::patch('/users/{id}/activate', [App\Http\Controllers\UserController::class, 'activate'])->name('users.activate');

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
        Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('admin.statistique');
        Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
        // Routes CRUD complètes pour les structures
        Route::resource('structures', StructureController::class);

        // Routes supplémentaires pour les fonctionnalités avancées
        Route::get('structures/search', [StructureController::class, 'search'])->name('structures.search');
        Route::get('structures/filter/type', [StructureController::class, 'filterByType'])->name('structures.filter.type');
    });
    // Routes pour l'importation CSV
    Route::prefix('import')->name('imports.')->group(function () {
        // Page principale d'importation
        Route::get('/', [ImportController::class, 'index'])->name('index');

        // Importation des structures
        Route::post('/structures', [ImportController::class, 'importStructures'])->name('structures');

        // Importation des objectifs stratégiques
        Route::post('/objectifs-strategiques', [ImportController::class, 'importObjectifsStrategiques'])->name('objectifs-strategiques');

        // Importation des objectifs spécifiques
        Route::post('/objectifs-specifiques', [ImportController::class, 'importObjectifsSpecifiques'])->name('objectifs-specifiques');

        // Téléchargement des modèles CSV
        Route::get('/template/{type}', [ImportController::class, 'downloadTemplate'])->name('template');

        // Validation d'un fichier CSV (AJAX)
        Route::post('/validate', [ImportController::class, 'validateCsv'])->name('validate');
        // imports
        Route::resource('imports', ImportController::class);
        Route::post('imports/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
        Route::post('imports/structure', [ImportController::class, 'importStructures'])->name('import.structures');
        Route::post('imports/objectifs-stra', [ImportController::class, 'importObjectifsStrategiques'])->name('import.objectifs-strategiques');
        Route::post('imports/objectif-spec', [ImportController::class, 'downloadTemplate'])->name('import.objectifs-specifiques');
    });

    // Activites
    Route::resource('activites', ActiviteController::class);
    Route::get('activites-hors-programme', [ActiviteController::class, 'indexActivitesHorsProgramme'])->name('rapportActivites.hors.programme');
    Route::get('activites-hors-programme/create', [ActiviteController::class, 'createHorsProgramme'])->name('rapportActivites.hors.programme.create');
    Route::post('activites-hors-programme/store', [ActiviteController::class, 'storeHorsProgramme'])->name('rapportActivites.hors.programme.store');
    Route::get('activites-hors-programme/show/{id}', [ActiviteController::class, 'showHorsProgramme'])->name('rapportActivites.hors.programme.show');
    Route::get('activites-hors-programme/edit/{id}', [ActiviteController::class, 'editHorsProgramme'])->name('rapportActivites.hors.programme.edit');
    Route::put('activites-hors-programme/update/{id}', [ActiviteController::class, 'updateHorsProgramme'])->name('rapportActivites.hors.programme.update');
    Route::post('activites-indicateur/store', [ActiviteController::class, 'storeActiviteIndicateur'])->name('activites.indicateur.store');
    Route::put('activites-indicateur/update/{id}', [ActiviteController::class, 'updateActiviteIndicateur'])->name('activites.indicateur.update');
    Route::delete('activites-hors-programme/delete/{id}', [ActiviteController::class, 'deleteHorsProgramme'])->name('rapportActivites.hors.programme.delete');
    route::resource('objectifStrategiques', App\Http\Controllers\ObjectifStrategiqueController::class);

    // Routes index (toujours accessible)
    Route::get('/programmebudgetaire', [ProgrammeBudgetaireController::class, 'index'])
        ->name('programmebudgetaire.index');
    Route::get('/programmebudgetaire/create', [ProgrammeBudgetaireController::class, 'create'])
        ->name('programmebudgetaire.create');
    // Toggle individuel (toujours accessible)
    Route::patch('/programmebudgetaire/{id}/toggle', [ProgrammeBudgetaireController::class, 'toggleEditable'])
        ->name('programmebudgetaire.toggleEditable');
    // Routes CRUD (édition et suppression protégées par le middleware)
    Route::group(['middleware' => ['check.programme.active']], function () {
        Route::get('/programmebudgetaire/{id}/edit', [ProgrammeBudgetaireController::class, 'edit'])->name('programmebudgetaire.edit');
        Route::patch('/programmebudgetaire/{id}', [ProgrammeBudgetaireController::class, 'update'])->name('programmebudgetaire.update');
        Route::delete('/programmebudgetaire/{id}', [ProgrammeBudgetaireController::class, 'destroy'])->name('programmebudgetaire.destroy');
        Route::get('/programmebudgetaire/validate/{id}', [ProgrammeBudgetaireController::class, 'validate'])->name('programmebudgetaire.validate');
    });
    Route::get('/programmebudgetaire/{id}', [ProgrammeBudgetaireController::class, 'show'])
        ->name('programmebudgetaire.show');
    Route::post('/programmebudgetaire', [ProgrammeBudgetaireController::class, 'store'])
        ->name('programmebudgetaire.store');
    //Route::resource('programmebudgetaire', ProgrammeBudgetaireController::class);
    //Route::resource('programmebudgetaire', ProgrammeBudgetaireController::class);
    //Dupication programme budgétaires
    Route::get('/programmes-budgetaires/duplicate', [ProgrammeBudgetaireController::class, 'createDuplicate'])
        ->name('programmes-budgetaires.create-duplicate');
    Route::post('/programmes-budgetaires/duplicate', [ProgrammeBudgetaireController::class, 'storeDuplicate'])
        ->name('programmes-budgetaires.store-duplicate');
    // OS routes
    route::resource('objectifStrategiques', App\Http\Controllers\ObjectifStrategiqueController::class);
    route::get('objectifStrategiques/pb/{id}', [ObjectifStrategiqueController::class, 'osByPB'])->name('programmebudgetaire.os');

    Route::get('suivis/create/{id}', [App\Http\Controllers\SuiviController::class, 'createByMIndicateur'])->name('suivis.createM');
    route::resource('suivis', App\Http\Controllers\SuiviController::class);
    Route::resource('actions', App\Http\Controllers\ActionController::class);

    Route::resource('objectifs-specifiques', App\Http\Controllers\ObjectifSpecifiqueController::class);
    route::get('objectifSpecifiqiques/os/{id}', [ObjectifStrategiqueController::class, 'osByPB'])->name('os.specifiques');

    Route::resource('indicateurs-programmes', App\Http\Controllers\IndicateurProgrammeController::class);

    Route::post('rapportActivites/visualiser', [App\Http\Controllers\RapportActiviteController::class, 'visualiser'])->name('rapportActivites.visualiser');
    Route::resource('programme-activites', App\Http\Controllers\ProgrammeActiviteController::class);
    Route::resource('rapportActivites', App\Http\Controllers\RapportActiviteController::class);
    Route::get('programme-activites/{id}/tableau', [App\Http\Controllers\ProgrammeActiviteController::class, 'tableau'])->name('programme-activites.tableau');
    Route::get('programme-activites/{id}/suivi', [ProgrammeActiviteController::class, 'suiviTableau'])->name('programme-activites.suivi');
    Route::get('programme-liste', [ProgrammeActiviteController::class, 'tableProgrammeActivite'])->name('programme.liste');
    Route::get('/programmes/export-excel', [ProgrammeActiviteController::class, 'exportExcel'])->name('programmes.export-excel');
    Route::get('/programmes/export-pdf', [ProgrammeActiviteController::class, 'exportPDF'])->name('programmes.export-pdf');
    Route::get('/programme-activites-liste', [ProgrammeActiviteController::class, 'listeProgrammeActivite'])->name('programme-activites.liste');

    // Routes pour les tableaux
    Route::prefix('admin')->middleware(['auth'])->group(function () {

        // Routes principales pour tableaux
        Route::get('/tableaux', [TableauController::class, 'index'])->name('tableaux.index');
        Route::get('/tableaux/create', [TableauController::class, 'create'])->name('tableaux.create');
        Route::post('/tableaux', [TableauController::class, 'store'])->name('tableaux.store');
        Route::get('/tableaux/{id}', [TableauController::class, 'show'])->name('tableaux.show');
        Route::get('/tableaux/{id}/edit', [TableauController::class, 'edit'])->name('tableaux.edit');
        Route::put('/tableaux/{id}', [TableauController::class, 'update'])->name('tableaux.update');
        Route::delete('/tableaux/{id}', [TableauController::class, 'destroy'])->name('tableaux.destroy');

        // Routes pour la structure des tableaux
        Route::get('/tableaux/{id}/structure', [TableauController::class, 'structureTableau'])->name('tableaux.structure');
        Route::post('/tableaux/{id}/add-ligne', [TableauController::class, 'addLigne'])->name('tableaux.add-ligne');
        Route::post('/tableaux/{id}/add-colonne', [TableauController::class, 'addColonne'])->name('tableaux.add-colonne');
        Route::put('/tableaux/{id}/update-ligne', [TableauController::class, 'updateLigne'])->name('tableaux.update-ligne');
        Route::put('/tableaux/{id}/update-colonne', [TableauController::class, 'updateColonne'])->name('tableaux.update-colonne');
        Route::delete('/tableaux/{id}/destroy-ligne', [TableauController::class, 'destroyLigne'])->name('tableaux.destroy-ligne');
        Route::delete('/tableaux/{id}/destroy-colonne', [TableauController::class, 'destroyColonne'])->name('tableaux.destroy-colonne');

        // Routes pour la saisie de données et canevas Excel
        Route::get('/tableaux/{id}/saisie', [TableauController::class, 'saisirDonnees'])->name('tableaux.saisie');
        Route::post('/tableaux/{id}/save-donnees', [TableauController::class, 'saveDonnees'])->name('tableaux.save-donnees');
        Route::get('/tableaux/{id}/canevas-excel', [TableauController::class, 'telechargerCanevas'])->name('tableaux.canevas-excel');
        Route::post('/tableaux/{id}/import-excel', [TableauController::class, 'importerCanevas'])->name('tableaux.import-excel');

        // Routes pour supprimer lignes et colonnes
        Route::delete('/lignes/{id}', [LigneTableauController::class, 'destroy'])->name('lignes.destroy');
        Route::delete('/colonnes/{id}', [ColonneTableauController::class, 'destroy'])->name('colonnes.destroy');

        // Routes categories de tableaux
        route::resource('categories', App\Http\Controllers\CategorieController::class);
        Route::post('/categories/{id}/attach-tableau', [CategorieController::class, 'attachTableau'])
            ->name('categories.attachTableau');

        // les assignations
        Route::get('/assignations', [AssignationController::class, 'index'])->name('assignations.index');
        Route::get('/assignations/create/{id}', [AssignationController::class, 'create'])->name('assignations.create');
        Route::post('/assignations/store/{id}', [AssignationController::class, 'store'])->name('assignations.store');
        Route::get('/assignations/edit/{id}', [AssignationController::class, 'edit'])->name('assignations.edit');
        Route::get('/assignations/show/{id}', [AssignationController::class, 'show'])->name('assignations.show');
        Route::put('/assignations/{assignation}', [AssignationController::class, 'update'])->name('assignations.update');
        Route::delete('/assignations/{assignation}', [AssignationController::class, 'destroy'])->name('assignations.destroy');
        Route::post('/assignations/{assignation}/status', [AssignationController::class, 'changeStatus'])->name('assignations.changeStatus');
        Route::get('/assignations/dashboard', [AssignationController::class, 'dashboard'])->name('assignations.dashboard');
        Route::get('tableaux/{id}/affecter', [TableauController::class, 'affecterStructures'])->name('tableaux.affecter');
        Route::post('tableaux/{id}/affecter', [TableauController::class, 'storeAffectation'])->name('tableaux.storeAffectation');
        Route::get('tableaux/affectation/multiple', [AssignationController::class, 'affectionM'])->name('tableaux.affectations');
        Route::post('tableaux/affectation/multiple/store', [AssignationController::class, 'storeM'])->name('assignations.storeM');
        Route::resource('annuaires', AnnuaireController::class);
        Route::resource('versions', VersionController::class);

        // Routes pour exporter l'annuaire en word, pdf et excel
        Route::get('/annuaire/export/pdf', [AnnuaireController::class, 'exportPDF'])->name('annuaire.export.pdf');
        Route::get('/annuaire/export/word', [AnnuaireController::class, 'exportWord'])->name('annuaire.export.word');
        Route::get('/annuaire/export/excel', [AnnuaireController::class, 'exportExcel'])->name('annuaire.export.excel');
    });
});


require __DIR__ . '/auth.php';
