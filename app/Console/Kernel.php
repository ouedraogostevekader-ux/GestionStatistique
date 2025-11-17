<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Définir les commandes artisan personnalisées de l'application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Définir la planification des commandes.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $today = now()->toDateString();

            // Avant la date début = 0
            \App\Models\Assignation::whereDate('date_debut', '>', $today)->update(['statut' => 0]);

            // Entre les deux = 1
            \App\Models\Assignation::whereDate('date_debut', '<=', $today)
                ->whereDate('date_fin', '>=', $today)
                ->update(['statut' => 1]);

            // Après la date fin = 3
            \App\Models\Assignation::whereDate('date_fin', '<', $today)->update(['statut' => 3]);
        })->daily();
        // $schedule->command('assignations:update-status')->daily();

    }
}
