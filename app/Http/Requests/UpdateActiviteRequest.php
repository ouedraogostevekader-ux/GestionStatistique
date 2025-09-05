<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActiviteRequest extends FormRequest
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
        ];
    }
}
