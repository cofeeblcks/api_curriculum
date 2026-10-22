<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UploadProfileAvatar
{
    public function execute(UploadedFile $file): Profile
    {
        $profile = Profile::query()->firstOrFail();

        if ($profile->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
        }

        $path = $file->store('profile', 'public');
        $profile->forceFill(['avatar_path' => $path])->save();

        return $profile->fresh();
    }
}
