<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'read' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'read.required' => 'El estado de lectura es requerido.',
            'read.boolean' => 'El estado de lectura debe ser verdadero o falso.',
        ];
    }
}
