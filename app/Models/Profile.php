<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'headline',
        'bio',
        'years_experience',
        'specialty',
        'location',
        'available',
        'logo_path',
        'avatar_path',
        'public_email',
        'phones',
        'socials',
    ];

    protected function casts(): array
    {
        return [
            'available' => 'boolean',
            'years_experience' => 'integer',
            'phones' => 'array',
            'socials' => 'array',
        ];
    }
}
