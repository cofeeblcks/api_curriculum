<?php

namespace App\Actions\Experiences;

use App\Models\Experience;

final class DeleteExperience
{
    public function execute(Experience $experience): void
    {
        $experience->delete();
    }
}
