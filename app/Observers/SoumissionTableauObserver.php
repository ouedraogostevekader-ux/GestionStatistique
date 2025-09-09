<?php
namespace App\Observers;

use App\Models\SoumissionTableau;
use App\Services\TableauCalculService;

class SoumissionTableauObserver
{
    protected $calculService;

    public function __construct(TableauCalculService $calculService)
    {
        $this->calculService = $calculService;
    }

    public function created(SoumissionTableau $soumission)
    {
        $this->recalculerTotaux($soumission);
    }

    public function updated(SoumissionTableau $soumission)
    {
        $this->recalculerTotaux($soumission);
    }

    public function deleted(SoumissionTableau $soumission)
    {
        $this->recalculerTotaux($soumission);
    }

    private function recalculerTotaux(SoumissionTableau $soumission)
    {
        // Appliquer les règles de gestion après modification
        $this->calculService->appliquerReglesGestion(
            $soumission->tableau_id,
            $soumission->assignation_id
        );
    }
}
