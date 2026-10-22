<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProfileLogoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Sube un PNG o SVG de hasta 2MB.',
            'logo.file' => 'Sube un PNG o SVG de hasta 2MB.',
            'logo.mimes' => 'Sube un PNG o SVG de hasta 2MB.',
            'logo.max' => 'Sube un PNG o SVG de hasta 2MB.',
        ];
    }
}
