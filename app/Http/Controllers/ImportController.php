<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\ObjectifsStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\ProgrammeBudgetaire;
use App\Models\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    /**
     * Importer les structures depuis un fichier CSV
     */
    public function importStructures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Fichier CSV invalide. Formats acceptés: .csv, .txt (max 10MB)');
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            // Récupérer les en-têtes
            $headers = array_shift($csvData);
            $headers = array_map('trim', $headers);

            // Valider les en-têtes requis
            $requiredHeaders = ['nom', 'sigle', 'type_structure'];
            $missingHeaders = array_diff($requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                return redirect()->back()->with('error', 'En-têtes manquants dans le CSV: ' . implode(', ', $missingHeaders));
            }

            $importedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $lineNumber = $index + 2; // +2 car index commence à 0 et on a enlevé les en-têtes

                if (empty(array_filter($row)))
                    continue; // Ignorer les lignes vides

                try {
                    $data = array_combine($headers, $row);
                    $data = array_map('trim', $data);

                    // Validation des données
                    if (empty($data['nom']) || empty($data['sigle']) || empty($data['type_structure'])) {
                        $errors[] = "Ligne $lineNumber: Champs obligatoires manquants (nom, sigle, type_structure)";
                        continue;
                    }

                    // Vérifier si la structure existe déjà
                    $existingStructure = Structure::where('nom', $data['nom'])
                        ->orWhere('sigle', $data['sigle'])
                        ->first();

                    if ($existingStructure) {
                        $errors[] = "Ligne $lineNumber: Structure '{$data['nom']}' ou sigle '{$data['sigle']}' existe déjà";
                        continue;
                    }

                    // Créer la structure
                    Structure::create([
                        'nom' => $data['nom'],
                        'sigle' => $data['sigle'],
                        'type_structure' => $data['type_structure'],
                    ]);

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne $lineNumber: " . $e->getMessage();
                    Log::error("Erreur import structure ligne $lineNumber: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "$importedCount structure(s) importée(s) avec succès";
            if (!empty($errors)) {
                $message .= ". Erreurs: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5)
                    $message .= " (+" . (count($errors) - 5) . " autres erreurs)";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur générale import structures: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Importer les objectifs stratégiques depuis un fichier CSV
     */
    public function importObjectifsStrategiques(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Fichier CSV invalide. Formats acceptés: .csv, .txt (max 10MB)');
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            $headers = array_shift($csvData);
            $headers = array_map('trim', $headers);

            // Valider les en-têtes requis
            $requiredHeaders = ['libelle', 'programme_budgetaire'];
            $missingHeaders = array_diff($requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                return redirect()->back()->with('error', 'En-têtes manquants dans le CSV: ' . implode(', ', $missingHeaders));
            }

            $importedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row)))
                    continue;

                try {
                    $data = array_combine($headers, $row);
                    $data = array_map('trim', $data);

                    if (empty($data['libelle']) || empty($data['programme_budgetaire'])) {
                        $errors[] = "Ligne $lineNumber: Champs obligatoires manquants (libelle, programme_budgetaire)";
                        continue;
                    }

                    // Trouver le programme budgétaire
                    $programmeBudgetaire = ProgrammeBudgetaire::where('libelle', $data['programme_budgetaire'])
                        ->orWhere('numero_programme', $data['programme_budgetaire'])
                        ->first();

                    if (!$programmeBudgetaire) {
                        $errors[] = "Ligne $lineNumber: Programme budgétaire '{$data['programme_budgetaire']}' non trouvé";
                        continue;
                    }

                    // Vérifier si l'objectif stratégique existe déjà
                    $existingObjectif = ObjectifsStrategique::where('libelle', $data['libelle'])
                        ->where('programme_budgetaire_id', $programmeBudgetaire->id)
                        ->first();

                    if ($existingObjectif) {
                        $errors[] = "Ligne $lineNumber: Objectif stratégique '{$data['libelle']}' existe déjà pour ce programme";
                        continue;
                    }

                    // Créer l'objectif stratégique
                    ObjectifsStrategique::create([
                        'libelle' => $data['libelle'],
                        'programme_budgetaire_id' => $programmeBudgetaire->id,
                    ]);

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne $lineNumber: " . $e->getMessage();
                    Log::error("Erreur import objectif stratégique ligne $lineNumber: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "$importedCount objectif(s) stratégique(s) importé(s) avec succès";
            if (!empty($errors)) {
                $message .= ". Erreurs: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5)
                    $message .= " (+" . (count($errors) - 5) . " autres erreurs)";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur générale import objectifs stratégiques: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Importer les objectifs spécifiques depuis un fichier CSV
     */
    public function importObjectifsSpecifiques(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Fichier CSV invalide. Formats acceptés: .csv, .txt (max 10MB)');
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            $headers = array_shift($csvData);
            $headers = array_map('trim', $headers);

            // Valider les en-têtes requis
            $requiredHeaders = ['libelle', 'action'];
            $missingHeaders = array_diff($requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                return redirect()->back()->with('error', 'En-têtes manquants dans le CSV: ' . implode(', ', $missingHeaders));
            }

            $importedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $lineNumber = $index + 2;

                if (empty(array_filter($row)))
                    continue;

                try {
                    $data = array_combine($headers, $row);
                    $data = array_map('trim', $data);

                    if (empty($data['libelle']) || empty($data['action'])) {
                        $errors[] = "Ligne $lineNumber: Champs obligatoires manquants (libelle, action)";
                        continue;
                    }

                    // Trouver l'action
                    $action = Action::where('libelle', $data['action'])->first();

                    if (!$action) {
                        $errors[] = "Ligne $lineNumber: Action '{$data['action']}' non trouvée";
                        continue;
                    }

                    // Vérifier si l'objectif spécifique existe déjà
                    $existingObjectif = ObjectifSpecifique::where('libelle', $data['libelle'])
                        ->where('action_id', $action->id)
                        ->first();

                    if ($existingObjectif) {
                        $errors[] = "Ligne $lineNumber: Objectif spécifique '{$data['libelle']}' existe déjà pour cette action";
                        continue;
                    }

                    // Créer l'objectif spécifique
                    ObjectifSpecifique::create([
                        'libelle' => $data['libelle'],
                        'action_id' => $action->id,
                    ]);

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne $lineNumber: " . $e->getMessage();
                    Log::error("Erreur import objectif spécifique ligne $lineNumber: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "$importedCount objectif(s) spécifique(s) importé(s) avec succès";
            if (!empty($errors)) {
                $message .= ". Erreurs: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5)
                    $message .= " (+" . (count($errors) - 5) . " autres erreurs)";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur générale import objectifs spécifiques: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un modèle CSV
     */
    public function downloadTemplate($type)
    {
        $templates = [
            'structures' => [
                'filename' => 'modele_structures.csv',
                'headers' => ['nom', 'sigle', 'type_structure'],
                'sample_data' => [
                    ['Direction des Ressources Humaines', 'DRH', 'Direction'],
                    ['Service Informatique', 'SI', 'Service'],
                    ['Département Finances', 'DF', 'Département']
                ]
            ],
            'objectifs-strategiques' => [
                'filename' => 'modele_objectifs_strategiques.csv',
                'headers' => ['libelle', 'programme_budgetaire'],
                'sample_data' => [
                    ['Améliorer la qualité des services', 'Programme Modernisation'],
                    ['Renforcer les capacités', 'Programme Formation'],
                    ['Optimiser les processus', 'Programme Efficacité']
                ]
            ],
            'objectifs-specifiques' => [
                'filename' => 'modele_objectifs_specifiques.csv',
                'headers' => ['libelle', 'action'],
                'sample_data' => [
                    ['Former le personnel administratif', 'Formation du personnel'],
                    ['Digitaliser les procédures', 'Modernisation des outils'],
                    ['Améliorer l\'accueil des usagers', 'Amélioration de l\'accueil']
                ]
            ]
        ];

        if (!isset($templates[$type])) {
            return redirect()->back()->with('error', 'Type de modèle non reconnu');
        }

        $template = $templates[$type];

        // Créer le contenu CSV
        $csvContent = implode(',', $template['headers']) . "\n";
        foreach ($template['sample_data'] as $row) {
            $csvContent .= '"' . implode('","', $row) . '"' . "\n";
        }

        // Retourner le fichier CSV
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $template['filename'] . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Valider un fichier CSV avant importation
     */
    public function validateCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'type' => 'required|in:structures,objectifs-strategiques,objectifs-specifiques'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Fichier ou type invalide'], 400);
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            $headers = array_shift($csvData);
            $headers = array_map('trim', $headers);

            $totalRows = count($csvData);
            $validRows = 0;
            $errors = [];

            // Définir les en-têtes requis selon le type
            $requiredHeadersMap = [
                'structures' => ['nom', 'sigle', 'type_structure'],
                'objectifs-strategiques' => ['libelle', 'programme_budgetaire'],
                'objectifs-specifiques' => ['libelle', 'action']
            ];

            $requiredHeaders = $requiredHeadersMap[$request->type];
            $missingHeaders = array_diff($requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                return response()->json([
                    'error' => 'En-têtes manquants: ' . implode(', ', $missingHeaders)
                ], 400);
            }

            // Valider quelques lignes pour avoir un aperçu
            foreach (array_slice($csvData, 0, 10) as $index => $row) {
                if (empty(array_filter($row)))
                    continue;

                $data = array_combine($headers, $row);
                $data = array_map('trim', $data);

                $isValid = true;
                foreach ($requiredHeaders as $header) {
                    if (empty($data[$header])) {
                        $errors[] = "Ligne " . ($index + 2) . ": Champ '$header' manquant";
                        $isValid = false;
                        break;
                    }
                }

                if ($isValid)
                    $validRows++;
            }

            return response()->json([
                'success' => true,
                'total_rows' => $totalRows,
                'valid_rows_sample' => $validRows,
                'errors_sample' => array_slice($errors, 0, 5),
                'headers' => $headers
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de validation: ' . $e->getMessage()], 500);
        }
    }
}