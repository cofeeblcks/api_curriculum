<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'yearsExperience' => $this->years_experience,
            'specialty' => $this->specialty,
            'location' => $this->location,
            'available' => $this->available,
            'logoUrl' => $this->logo_path ? url(Storage::url($this->logo_path)) : null,
            'avatarUrl' => $this->avatar_path ? url(Storage::url($this->avatar_path)) : null,
            'publicEmail' => $this->public_email,
            'phones' => $this->phones ?? [],
            'socials' => $this->socials ?? [],
        ];
    }
}
