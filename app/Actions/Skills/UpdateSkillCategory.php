<?php

namespace App\Actions\Skills;

use App\Models\SkillCategory;
use App\Traits\Models\FillModelData;

final class UpdateSkillCategory
{
    use FillModelData;

    public function execute(SkillCategory $category, array $data): SkillCategory
    {
        $category->fill($this->fillData(SkillCategory::class, $data));
        $category->save();

        return $category->fresh('skills');
    }
}
