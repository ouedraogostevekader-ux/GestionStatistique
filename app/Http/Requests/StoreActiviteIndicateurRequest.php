<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActiviteIndicateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adapter si besoin
    }

    // Dans StoreActiviteRequest.php

    public function rules()
    {
        return [
            'libelle' => 'required|string|max:500',
            'cout_previsionnel' => 'required|numeric|min:0',
            'annee' => 'required|integer|min:2000|max:2100',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',
            'hors_programme' => 'nullable|boolean',
            'periodes' => 'required|array|min:1',
            'periodes.*' => 'exists:periodes,id',

            // Validation des indicateurs
            'indicateurs' => 'nullable|array|max:10', // Maximum 10 indicateurs
            'indicateurs.*.libelle' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.unite_mesure' => 'required_with:indicateurs.*|string|max:100',
            'indicateurs.*.valeur_ref' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.valeur_cible' => 'required_with:indicateurs.*|string|max:255',
            'indicateurs.*.annee' => 'required_with:indicateurs.*|integer|min:2000|max:2100',
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé de l\'activité est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 500 caractères.',
            'cout_previsionnel.required' => 'Le coût prévisionnel est obligatoire.',
            'cout_previsionnel.numeric' => 'Le coût prévisionnel doit être un nombre.',
            'cout_previsionnel.min' => 'Le coût prévisionnel doit être positif.',
            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2000.',
            'annee.max' => 'L\'année ne peut pas dépasser 2100.',
            'objectif_specifique_id.required' => 'L\'objectif spécifique est obligatoire.',
            'objectif_specifique_id.exists' => 'L\'objectif spécifique sélectionné n\'existe pas.',
            'periodes.required' => 'Au moins une période doit être sélectionnée.',
            'periodes.array' => 'Les périodes doivent être un tableau.',
            'periodes.min' => 'Au moins une période doit être sélectionnée.',
            'periodes.*.exists' => 'Une des périodes sélectionnées n\'existe pas.',

            // Messages pour les indicateurs
            'indicateurs.array' => 'Les indicateurs doivent être un tableau.',
            'indicateurs.max' => 'Vous ne pouvez pas ajouter plus de 10 indicateurs.',
            'indicateurs.*.libelle.required_with' => 'Le libellé de l\'indicateur est obligatoire.',
            'indicateurs.*.libelle.max' => 'Le libellé de l\'indicateur ne peut pas dépasser 255 caractères.',
            'indicateurs.*.unite_mesure.required_with' => 'L\'unité de mesure de l\'indicateur est obligatoire.',
            'indicateurs.*.unite_mesure.max' => 'L\'unité de mesure ne peut pas dépasser 100 caractères.',
            'indicateurs.*.valeur_ref.required_with' => 'La valeur de référence de l\'indicateur est obligatoire.',
            'indicateurs.*.valeur_ref.max' => 'La valeur de référence ne peut pas dépasser 255 caractères.',
            'indicateurs.*.valeur_cible.required_with' => 'La valeur cible de l\'indicateur est obligatoire.',
            'indicateurs.*.valeur_cible.max' => 'La valeur cible ne peut pas dépasser 255 caractères.',
            'indicateurs.*.annee.required_with' => 'L\'année de l\'indicateur est obligatoire.',
            'indicateurs.*.annee.integer' => 'L\'année de l\'indicateur doit être un nombre entier.',
            'indicateurs.*.annee.min' => 'L\'année de l\'indicateur doit être supérieure ou égale à 2000.',
            'indicateurs.*.annee.max' => 'L\'année de l\'indicateur ne peut pas dépasser 2100.',
        ];
    }

    public function attributes()
    {
        return [
            'libelle' => 'libellé',
            'cout_previsionnel' => 'coût prévisionnel',
            'annee' => 'année',
            'objectif_specifique_id' => 'objectif spécifique',
            'periodes' => 'périodes',
            'indicateurs.*.libelle' => 'libellé de l\'indicateur',
            'indicateurs.*.unite_mesure' => 'unité de mesure',
            'indicateurs.*.valeur_ref' => 'valeur de référence',
            'indicateurs.*.valeur_cible' => 'valeur cible',
            'indicateurs.*.annee' => 'année de l\'indicateur',
        ];
    }
}
