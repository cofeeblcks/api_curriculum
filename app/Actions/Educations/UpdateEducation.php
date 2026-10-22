<?php

namespace App\Actions\Educations;

use App\Models\Education;
use App\Traits\Models\FillModelData;

final class UpdateEducation
{
    use FillModelData;

    public function execute(Education $education, array $data): Education
    {
        $education->fill($this->fillData(Education::class, $data));
        $education->save();

        return $education->fresh();
    }
}
