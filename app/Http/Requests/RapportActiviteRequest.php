<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RapportActiviteRequest extends FormRequest
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
            //'programme_activite_id' => 'required|string',
            'libelle' => 'required|string',
            'periode_rapport' => 'required|string',
            //'date_rapport' => 'required|date',
            //'annee' => 'required|integer',
            'file' => 'nullable',
            'file_pdf' => 'nullable',
        ];
    }
}
