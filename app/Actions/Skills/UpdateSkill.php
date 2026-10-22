<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\Models\FillModelData;

final class UpdateSkill
{
    use FillModelData;

    public function execute(Skill $skill, array $data): Skill
    {
        $skill->fill($this->fillData(Skill::class, $data));
        $skill->save();

        return $skill->fresh('category');
    }
}
