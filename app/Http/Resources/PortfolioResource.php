<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile' => new ProfileResource($this->resource['profile']),
            'skillCategories' => SkillCategoryResource::collection($this->resource['skillCategories']),
            'experiences' => ExperienceResource::collection($this->resource['experiences']),
            'educations' => EducationResource::collection($this->resource['educations']),
        ];
    }
}
