<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuiviRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'resultat_atteint' => 'required|numeric|min:0',
            'cout_effectif'=>'nullable|numeric|min:0',
            'observations'=>'nullable|string',
            'periode'=>'required|string',
            'indicateur_programme_id'=>'required|string|exists:indicateurs_programmes,id',
        ];
    }
}
