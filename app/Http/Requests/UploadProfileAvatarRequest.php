<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProfileAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Sube una imagen PNG, JPG o WEBP de hasta 2MB.',
            'avatar.file' => 'Sube una imagen PNG, JPG o WEBP de hasta 2MB.',
            'avatar.mimes' => 'Sube una imagen PNG, JPG o WEBP de hasta 2MB.',
            'avatar.max' => 'Sube una imagen PNG, JPG o WEBP de hasta 2MB.',
        ];
    }
}
