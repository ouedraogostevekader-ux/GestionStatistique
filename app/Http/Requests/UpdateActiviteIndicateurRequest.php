<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActiviteIndicateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adapter si besoin
    }

    public function rules(): array
    {
        return [
            'libelle' => 'required|string|max:255',
            'periodes' => 'sometimes|array',
            'periodes.*' => 'exists:periodes,id',
            'annee' => 'required|integer|min:2000|max:2100',
            'cout_previsionnel' => 'required|numeric|min:0',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',

            // Indicateurs
            // Validation des indicateurs
            'indicateurs' => 'nullable|array|max:10', // Maximum 10 indicateurs
            'indicateurs.*.libelle' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.unite_mesure' => 'required_with:indicateurs.*|string|max:100',
            'indicateurs.*.valeur_ref' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.valeur_cible' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.annee' => 'required_with:indicateurs.*|integer|min:2000|max:2100',

        ];
    }
}
