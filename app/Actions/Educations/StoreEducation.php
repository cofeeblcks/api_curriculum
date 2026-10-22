<?php

namespace App\Actions\Educations;

use App\Models\Education;
use App\Traits\Models\FillModelData;

final class StoreEducation
{
    use FillModelData;

    public function execute(array $data): Education
    {
        $education = new Education;
        $education->fill($this->fillData(Education::class, $data));
        $education->save();

        return $education->fresh();
    }
}
