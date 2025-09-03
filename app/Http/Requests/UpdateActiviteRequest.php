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
            'cout_previsionnel' => 'sometimes|numeric|min:0',
            'periode' => 'sometimes|string|max:255',
            'libelle' => 'sometimes|string|max:255',
            'objectif_specifique_id' => 'sometimes|exists:objectifs_specifiques,id',
        ];
    }
}
