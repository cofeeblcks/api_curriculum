<?php

namespace App\Actions\Skills;

use App\Models\SkillCategory;

final class DeleteSkillCategory
{
    public function execute(SkillCategory $category): void
    {
        $category->delete();
    }
}
