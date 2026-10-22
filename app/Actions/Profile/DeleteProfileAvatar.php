<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

final class DeleteProfileAvatar
{
    public function execute(): Profile
    {
        $profile = Profile::query()->firstOrFail();

        if ($profile->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
        }

        $profile->forceFill(['avatar_path' => null])->save();

        return $profile->fresh();
    }
}
