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

            'email' =>  ['required', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                //'regex:/[A-Z]/',      // must contain at least one uppercase letter
                //'regex:/[0-9]/',      // must contain at least one digit
                //'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'password_confirm' => 'same:password',
            'roles' => 'required',
            'confirm-password' => 'required|same:password',
            'nom' => [
                'required',
                'string',
                'regex:/^[A-ZÀ-ÖÙ-Ý][a-zà-öù-ý\-\' ]{1,49}$/', // commence par majuscule, accepte accents, tirets et apostrophes
                'min:2',
                'max:50'
            ],

            'prenom' => [
                'required',
                'string',
                'regex:/^[A-ZÀ-ÖÙ-Ý][a-zà-öù-ý\-\' ]{1,49}$/',
                'min:2',
                'max:50'
            ],

            'fonction' => [
                'required',
                'string',
                'regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\']+$/', // autorise lettres, chiffres, tirets et apostrophes
                'min:2',
                'max:100'
            ],
            'matricule' => [
                'required',
                'regex:/^\d+[A-Za-z]$/', //  un ou plusieurs chiffres suivis d'une lettre
                'min:2',
                'max:100',
                'string',
                Rule::unique('users', 'matricule')->whereNull('deleted_at')
            ],
            'telephone' => ['required', 'string', Rule::unique('users', 'telephone')->whereNull('deleted_at')],
            'structure_id' => 'required|string',
            // 'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'

        ];
    }

    public function messages()
    {
        return [

            'email.unique' => 'Ce email est déjà utilisé',
            'nom.required' => 'Le nom est obligatoire',
            'prenom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.regex' => 'Le nom doit commencer par une majuscule et ne contenir que des lettres, espaces, tirets ou apostrophes.',
            'nom.min' => 'Le nom doit comporter au moins :min caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser :max caractères.',

            'prenom.required' => 'Le prénom est obligatoire',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.regex' => 'Le prénom doit commencer par une majuscule et ne contenir que des lettres, espaces, tirets ou apostrophes.',
            'prenom.min' => 'Le prénom doit comporter au moins :min caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser :max caractères.',

            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Ce matricule est déjà utilisé',
            'matricule.redex' => 'Le matricule doit comporter des chiffres suivie d\'une lettre masjuscule ou minuscule',
            'fonction.min' => 'Le matricule doit comporter au moins :min caractères.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé',
            'telephone.required' => 'Le téléphone est obligatoire',

            'fonction.required' => 'Le fonction est obligatoire',
            'fonction.string' => 'La fonction doit être une chaîne de caractères.',
            'fonction.regex' => 'La fonction ne peut contenir que des lettres, chiffres, espaces, tirets ou apostrophes.',
            'fonction.min' => 'La fonction doit comporter au moins :min caractères.',
            'fonction.max' => 'La fonction ne peut pas dépasser :max caractères.',

            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères',
            'password.same' => 'Les saisies ne sont pas conformes',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre',
            'confirm-password.required' => 'Veuillez confirmer le mot de passe',
            // 'photo.required' => 'Veuillez sélectionner une image',
            // 'photo.image' => 'Le fichier doit être une image',
            // 'photo.mimes' => 'Formats acceptés: jpeg, png, jpg, gif',
            // 'photo.max' => 'La taille maximale est de 2MB'

        ];
    }
}
