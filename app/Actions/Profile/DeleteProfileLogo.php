<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

final class DeleteProfileLogo
{
    public function execute(): Profile
    {
        $profile = Profile::query()->firstOrFail();

        if ($profile->logo_path) {
            Storage::disk('public')->delete($profile->logo_path);
        }

        $profile->forceFill(['logo_path' => null])->save();

        return $profile->fresh();
    }
}
