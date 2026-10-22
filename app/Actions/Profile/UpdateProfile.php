<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;

final class UpdateProfile
{
    use FillModelData;

    public function execute(array $data): Profile
    {
        return DB::transaction(function () use ($data) {
            $profile = Profile::query()->first() ?? new Profile;
            $profile->fill($this->fillData(Profile::class, $data));
            $profile->save();

            return $profile->fresh();
        });
    }
}
