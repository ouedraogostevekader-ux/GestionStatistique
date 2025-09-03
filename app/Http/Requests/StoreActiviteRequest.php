<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActiviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adapter si besoin
    }

    public function rules(): array
    {
        return [
            'cout_previsionnel' => 'required|numeric|min:0',
            'periode' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'objectif_specifique_id' => 'required|exists:objectifs_specifiques,id',
        ];
    }
}
