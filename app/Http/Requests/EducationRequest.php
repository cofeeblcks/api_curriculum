<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EducationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['pregrado', 'certificacion', 'tecnico', 'curso'])],
            'title' => ['required', 'string', 'max:255'],
            'institution' => ['required', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de formación es requerido.',
            'type.in' => 'El tipo debe ser pregrado, certificacion, tecnico o curso.',
            'title.required' => 'El título es requerido.',
            'institution.required' => 'La institución es requerida.',
        ];
    }
}
