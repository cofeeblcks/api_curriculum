<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\Models\FillModelData;

final class UpdateExperience
{
    use FillModelData;

    public function execute(Experience $experience, array $data): Experience
    {
        $experience->fill($this->fillData(Experience::class, $data));
        $experience->save();

        return $experience->fresh();
    }
}
