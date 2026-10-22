<?php

namespace App\Actions\Educations;

use App\Models\Education;

final class DeleteEducation
{
    public function execute(Education $education): void
    {
        $education->delete();
    }
}
