<?php

namespace App\Actions\Skills;

use App\Models\Skill;

final class DeleteSkill
{
    public function execute(Skill $skill): void
    {
        $skill->delete();
    }
}
