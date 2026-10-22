<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\Models\FillModelData;

final class StoreExperience
{
    use FillModelData;

    public function execute(array $data): Experience
    {
        $experience = new Experience;
        $experience->fill($this->fillData(Experience::class, $data));
        $experience->save();

        return $experience->fresh();
    }
}
