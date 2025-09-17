<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Contracts\View\View;
use Termwind\Components\Li;

class RapportActiviteExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $periode;
    protected $annee;
    protected $date;
    protected $libelle;

    public function __construct($data, $periode, $annee, $date=null, $libelle)
    {
        $this->data = $data;
        $this->periode = $periode;
        $this->annee = $annee;
        $this->date = $date ?: now();
        $this->libelle = $libelle;

    }

    public function view(): View
    {
        return view('rapportActivites.excel', [
            'data' => $this->data,
            'periode' => $this->periode,
            'annee' => $this->annee,
            'date' => $this->date,
            'libelle' => $this->libelle,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Style pour l'en-tête
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '7CB342']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style pour le titre du programme
        $sheet->getStyle('A3:G3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'E8F5E8']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style pour les objectifs stratégiques
        // $sheet->getStyle('A3:A4')->applyFromArray([
        //     'font' => ['bold' => true],
        //     'fill' => [
        //         'fillType' => Fill::FILL_SOLID,
        //         'color' => ['rgb' => 'F0F8FF']
        //     ],
        //     'alignment' => [
        //         'horizontal' => Alignment::HORIZONTAL_CENTER,
        //         'vertical' => Alignment::VERTICAL_CENTER,
        //         'wrapText' => true
        //     ],
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => Border::BORDER_THIN,
        //             'color' => ['rgb' => '000000']
        //         ]
        //     ]
        // ]);

        // Appliquer les styles aux données
        // $lastRow = $sheet->getHighestRow();
        // for ($row = 3; $row <= $lastRow; $row++) {
        //     $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
        //         'borders' => [
        //             'allBorders' => [
        //                 'borderStyle' => Border::BORDER_THIN,
        //                 'color' => ['rgb' => '000000']
        //             ]
        //         ],
        //         'alignment' => [
        //             'vertical' => Alignment::VERTICAL_CENTER,
        //             'wrapText' => true
        //         ]
        //     ]);
        // }

        // Définir la hauteur des lignes
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('2')->setRowHeight(40);
        $sheet->getRowDimension('3')->setRowHeight(30);
        
        // Fusionner les cellules
        //$sheet->mergeCells('A2:G2'); // Titre du programme
        //$sheet->mergeCells('A3:A4'); // Objectif stratégique
        
        // Fusionner les cellules pour les activités (exemple)
        //$sheet->mergeCells('A5:A8'); // Objectif Opérationnel
        //$sheet->mergeCells('A12:A13'); // Activité principale

        return [];
    }
}
