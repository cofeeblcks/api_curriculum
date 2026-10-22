<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'skillCategoryId' => $this->skill_category_id,
            'name' => $this->name,
            'note' => $this->note,
            'sortOrder' => $this->sort_order,
            'category' => new SkillCategoryResource($this->whenLoaded('category')),
        ];
    }
}
