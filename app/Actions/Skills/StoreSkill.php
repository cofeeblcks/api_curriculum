<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\Models\FillModelData;

final class StoreSkill
{
    use FillModelData;

    public function execute(array $data): Skill
    {
        $skill = new Skill;
        $skill->fill($this->fillData(Skill::class, $data));
        $skill->save();

        return $skill->fresh('category');
    }
}
