<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActiviteHorsProgrammeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adapter si besoin
    }
    public function rules()
    {
        $rules = [
            'libelle' => 'required|string|max:500',
            'annee' => 'required|integer|min:2000|max:2100',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',
            'hors_programme' => 'nullable|boolean',
            'periodes' => 'required|array|min:1',
            'periodes.*' => 'exists:periodes,id',
        ];

        // Si c'est une activité du programme (pas hors programme)
        if (!$this->boolean('hors_programme')) {
            // Le coût prévisionnel est obligatoire pour les activités du programme
            $rules['cout_previsionnel'] = 'required|numeric|min:0';

            // Les indicateurs sont obligatoires pour les activités du programme
            $rules['indicateurs'] = 'required|array|min:1|max:10';
            $rules['indicateurs.*.libelle'] = 'required|string|max:255';
            $rules['indicateurs.*.unite_mesure'] = 'required|string|max:100';
            $rules['indicateurs.*.valeur_ref'] = 'required|string|max:255';
            $rules['indicateurs.*.valeur_cible'] = 'required|string|max:255';
            $rules['indicateurs.*.annee'] = 'required|integer|min:2000|max:2100';
        } else {
            // Pour les activités hors programme, le coût et les indicateurs sont optionnels
            $rules['cout_previsionnel'] = 'nullable|numeric|min:0';
            $rules['indicateurs'] = 'nullable|array|max:10';
            $rules['indicateurs.*.libelle'] = 'nullable|string|max:255';
            $rules['indicateurs.*.unite_mesure'] = 'nullable|string|max:100';
            $rules['indicateurs.*.valeur_ref'] = 'nullable|string|max:255';
            $rules['indicateurs.*.valeur_cible'] = 'nullable|string|max:255';
            $rules['indicateurs.*.annee'] = 'nullable|integer|min:2000|max:2100';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé de l\'activité est obligatoire.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 500 caractères.',
            'cout_previsionnel.required' => 'Le coût prévisionnel est obligatoire pour les activités du programme.',
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

            // Messages pour les indicateurs (activités du programme)
            'indicateurs.required' => 'Au moins un indicateur est obligatoire pour les activités du programme.',
            'indicateurs.array' => 'Les indicateurs doivent être un tableau.',
            'indicateurs.min' => 'Au moins un indicateur doit être défini pour les activités du programme.',
            'indicateurs.max' => 'Vous ne pouvez pas ajouter plus de 10 indicateurs.',
            'indicateurs.*.libelle.required' => 'Le libellé de l\'indicateur est obligatoire.',
            'indicateurs.*.libelle.max' => 'Le libellé de l\'indicateur ne peut pas dépasser 255 caractères.',
            'indicateurs.*.unite_mesure.required' => 'L\'unité de mesure de l\'indicateur est obligatoire.',
            'indicateurs.*.unite_mesure.max' => 'L\'unité de mesure ne peut pas dépasser 100 caractères.',
            'indicateurs.*.valeur_ref.required' => 'La valeur de référence de l\'indicateur est obligatoire.',
            'indicateurs.*.valeur_ref.max' => 'La valeur de référence ne peut pas dépasser 255 caractères.',
            'indicateurs.*.valeur_cible.required' => 'La valeur cible de l\'indicateur est obligatoire.',
            'indicateurs.*.valeur_cible.max' => 'La valeur cible ne peut pas dépasser 255 caractères.',
            'indicateurs.*.annee.required' => 'L\'année de l\'indicateur est obligatoire.',
            'indicateurs.*.annee.integer' => 'L\'année de l\'indicateur doit être un nombre entier.',
            'indicateurs.*.annee.min' => 'L\'année de l\'indicateur doit être supérieure ou égale à 2000.',
            'indicateurs.*.annee.max' => 'L\'année de l\'indicateur ne peut pas dépasser 2100.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérification personnalisée pour les activités hors programme
            if ($this->boolean('hors_programme')) {
                // Pour les activités hors programme, si des indicateurs sont fournis,
// ils doivent être complets (tous les champs remplis ou tous vides)
                $indicateurs = $this->input('indicateurs', []);

                foreach ($indicateurs as $index => $indicateur) {
                    $hasAnyField = !empty($indicateur['libelle']) ||
                        !empty($indicateur['unite_mesure']) ||
                        !empty($indicateur['valeur_ref']) ||
                        !empty($indicateur['valeur_cible']);

                    if ($hasAnyField) {
                        // Si au moins un champ est rempli, tous doivent l'être
                        if (empty($indicateur['libelle'])) {
                            $validator->errors()->add(
                                "indicateurs.{$index}.libelle",
                                'Le libellé est obligatoire si vous remplissez un indicateur.'
                            );
                        }
                        if (empty($indicateur['unite_mesure'])) {
                            $validator->errors()->add(
                                "indicateurs.{$index}.unite_mesure",
                                'L\'unité de mesure est obligatoire si vous remplissez un indicateur.'
                            );
                        }
                        if (empty($indicateur['valeur_ref'])) {
                            $validator->errors()->add(
                                "indicateurs.{$index}.valeur_ref",
                                'La valeur de référence est obligatoire si vous remplissez un indicateur.'
                            );
                        }
                        if (empty($indicateur['valeur_cible'])) {
                            $validator->errors()->add(
                                "indicateurs.{$index}.valeur_cible",
                                'La valeur cible est obligatoire si vous remplissez un indicateur.'
                            );
                        }
                    }
                }
            }
        });
    }
}