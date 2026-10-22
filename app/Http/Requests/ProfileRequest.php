<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string'],
            'yearsExperience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'available' => ['nullable', 'boolean'],
            'publicEmail' => ['nullable', 'email', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*' => ['string', 'max:50'],
            'socials' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido.',
            'publicEmail.email' => 'El correo público debe ser un correo válido.',
        ];
    }
}
