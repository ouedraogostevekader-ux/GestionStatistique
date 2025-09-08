<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'email' => ['required', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'roles' => 'required',
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'fonction' => 'required|string',
            'matricule' => ['required', 'string', Rule::unique('users', 'matricule')->whereNull('deleted_at')],
            'telephone' => 'required|string',
            'structure_id' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'nom.*' => 'Le nom est obligatoire',
            'prenom.*' => 'Le prénom est obligatoire',
            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Ce matricule est déjà utilisé',
            'telephone.*' => 'Le telephone est obligatoire',
        ];
    }
}
