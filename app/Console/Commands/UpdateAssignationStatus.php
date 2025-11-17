<?php

namespace App\Console\Commands;

use App\Models\Assignation;
use Illuminate\Console\Command;

class UpdateAssignationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-assignation-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Assignation::all()->each(function ($assignation) {
            $assignation->update([
                'statut' => Assignation::determinerStatut($assignation->date_debut, $assignation->date_fin),
            ]);
        });
    }
}
