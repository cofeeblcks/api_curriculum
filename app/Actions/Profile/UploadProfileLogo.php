<?php

namespace App\Actions\Profile;

use App\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UploadProfileLogo
{
    public function execute(UploadedFile $file): Profile
    {
        $profile = Profile::query()->firstOrFail();

        if ($profile->logo_path) {
            Storage::disk('public')->delete($profile->logo_path);
        }

        $path = $file->store('profile', 'public');
        $profile->forceFill(['logo_path' => $path])->save();

        return $profile->fresh();
    }
}
