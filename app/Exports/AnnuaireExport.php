<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnnuaireExport implements FromView, ShouldAutoSize
{
    protected array $donnees;

    public function __construct(array $donnees)
    {
        $this->donnees = $donnees;
    }

    public function view(): View
    {
        return view('annuaires.excel', [
            'version' => $this->donnees['version'],
            'tableaux' => $this->donnees['tableaux'],
            'matrice' => $this->donnees['matrice'],
            'totauxLignes' => $this->donnees['totauxLignes'],
            'totauxColonnes' => $this->donnees['totauxColonnes'],
            'totalGeneral' => $this->donnees['totalGeneral'],
        ]);
    }
}
