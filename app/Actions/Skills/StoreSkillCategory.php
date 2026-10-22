<?php

namespace App\Actions\Skills;

use App\Models\SkillCategory;
use App\Traits\Models\FillModelData;

final class StoreSkillCategory
{
    use FillModelData;

    public function execute(array $data): SkillCategory
    {
        $category = new SkillCategory;
        $category->fill($this->fillData(SkillCategory::class, $data));
        $category->save();

        return $category->fresh('skills');
    }
}
