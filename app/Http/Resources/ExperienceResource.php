<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'startsOn' => $this->starts_on?->format('Y-m-d'),
            'endsOn' => $this->ends_on?->format('Y-m-d'),
            'description' => $this->description,
            'techTags' => $this->tech_tags ?? [],
            'sortOrder' => $this->sort_order,
        ];
    }
}
