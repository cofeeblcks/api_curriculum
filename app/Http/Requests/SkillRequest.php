<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'skillCategoryId' => ['required', 'integer', 'exists:skill_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'skillCategoryId.required' => 'La categoría es requerida.',
            'skillCategoryId.exists' => 'La categoría no existe.',
            'name.required' => 'El nombre de la habilidad es requerido.',
        ];
    }
}
