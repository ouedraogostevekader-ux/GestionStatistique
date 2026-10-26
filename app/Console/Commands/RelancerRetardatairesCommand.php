<?php

namespace App\Console\Commands;

use App\Mail\RelanceAssignationMail;
use App\Models\Assignation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RelancerRetardatairesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:relancer-retardataires 
                            {--force : Forcer l\'envoi même si une relance a déjà été envoyée aujourd\'hui}
                            {--jours=3 : Nombre de jours avant l\'échéance pour déclencher l\'alerte préventive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alerte et relance les structures retardataires ou dont l\'échéance de soumission approche';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== [MID / DGESS] Lancement du traitement des relances statistiques ===");

        $force = (bool) $this->option('force');
        $jours = (int) $this->option('jours');

        $assignations = Assignation::with(['tableau', 'structure', 'version'])
            ->where('statut', '!=', 3) // Exclure les assignations déjà validées
            ->whereNotNull('date_fin')
            ->get();

        $candidats = $assignations->filter(function ($assignation) use ($jours) {
            return $assignation->isEnRetard() || $assignation->isEcheanceProche($jours);
        });

        if ($candidats->isEmpty()) {
            $this->info("Aucune structure retardataire ou en échéance critique détectée.");
            return Command::SUCCESS;
        }

        $this->info("{$candidats->count()} assignation(s) identifiée(s) pour relance.");

        $envoyees = 0;
        $ignoreesAntiSpam = 0;
        $erreurs = 0;

        foreach ($candidats as $assignation) {
            $typeRelance = $assignation->isEnRetard() ? 'retard' : 'echeance_proche';

            // Anti-spam 24h
            if (!$force && $assignation->derniere_relance_at && Carbon::parse($assignation->derniere_relance_at)->isToday()) {
                $ignoreesAntiSpam++;
                $this->line("  [Anti-Spam] Déjà relancée aujourd'hui : #{$assignation->id} - {$assignation->structure->nom}");
                continue;
            }

            // Récupérer les destinataires de la structure
            $users = User::where('structure_id', $assignation->structure_id)->get();
            $emails = $users->pluck('email')->filter()->all();

            if (empty($emails)) {
                // Utiliser email par défaut si la structure n'a pas encore de compte utilisateur
                $emails = [config('mail.from.address', 'direction.statistique@mid.gov.bf')];
            }

            try {
                foreach ($emails as $email) {
                    Mail::to($email)->send(new RelanceAssignationMail($assignation, $typeRelance));
                }

                $assignation->update([
                    'derniere_relance_at' => Carbon::now(),
                    'relances_count' => ($assignation->relances_count ?? 0) + 1,
                ]);

                $envoyees++;
                $label = $typeRelance === 'retard' ? 'RETARD' : 'ÉCHÉANCE PROCHE';
                $this->info("  ✓ [{$label}] Relance transmise à {$assignation->structure->sigle} ({$assignation->tableau->libelle})");
            } catch (\Throwable $e) {
                $erreurs++;
                Log::error("Erreur lors de l'envoi de la relance pour l'assignation {$assignation->id}: " . $e->getMessage());
                $this->error("  ✗ Échec d'envoi pour l'assignation #{$assignation->id} : " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['Relances envoyées avec succès', $envoyees],
                ['Ignorées (Anti-Spam 24h)', $ignoreesAntiSpam],
                ['Erreurs d\'envoi', $erreurs],
            ]
        );

        return Command::SUCCESS;
    }
}
