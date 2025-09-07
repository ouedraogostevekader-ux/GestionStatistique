<?php

// app/Exports/ProgrammeTableauExport.php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProgrammeTableauExport implements FromView, WithStyles
{
    protected $programmes;

    public function __construct($programmes)
    {
        $this->programmes = $programmes;
    }

    public function view(): View
    {
        return view('prog_activite.export-excel', [
            'programmes' => $this->programmes
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => '007bff']]],
            // Ajoutez d'autres styles selon vos besoins
        ];
    }
}
