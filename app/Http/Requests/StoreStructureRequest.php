<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStructureRequest extends FormRequest
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
        $rules = [
            'nom' => 'required|string|max:255',
            'sigle' => 'required|string|max:50',
            'type_structure' => 'required|string|max:255',
        ];

        // Si on est en mode édition, on exclut l'ID actuel pour l'unicité
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $structureId = $this->route('structure');
            $rules['nom'] .= '|unique:structures,nom,' . $structureId;
            $rules['sigle'] .= '|unique:structures,sigle,' . $structureId;
        } else {
            $rules['nom'] .= '|unique:structures,nom';
            $rules['sigle'] .= '|unique:structures,sigle';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la structure est obligatoire.',
            'nom.string' => 'Le nom de la structure doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de la structure ne peut pas dépasser 255 caractères.',
            'nom.unique' => 'Ce nom de structure existe déjà.',

            'sigle.required' => 'Le sigle de la structure est obligatoire.',
            'sigle.string' => 'Le sigle de la structure doit être une chaîne de caractères.',
            'sigle.max' => 'Le sigle de la structure ne peut pas dépasser 50 caractères.',
            'sigle.unique' => 'Ce sigle existe déjà.',

            'type_structure.required' => 'Le type de structure est obligatoire.',
            'type_structure.string' => 'Le type de structure doit être une chaîne de caractères.',
            'type_structure.max' => 'Le type de structure ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom de la structure',
            'sigle' => 'sigle',
            'type_structure' => 'type de structure',
        ];
    }
}