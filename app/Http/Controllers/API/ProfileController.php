<?php

namespace App\Http\Controllers\API;

use App\Actions\Profile\DeleteProfileAvatar;
use App\Actions\Profile\DeleteProfileLogo;
use App\Actions\Profile\UpdateProfile;
use App\Actions\Profile\UploadProfileAvatar;
use App\Actions\Profile\UploadProfileLogo;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UploadProfileAvatarRequest;
use App\Http\Requests\UploadProfileLogoRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Perfil';

    public function show(): JsonResponse
    {
        try {
            $profile = Profile::query()->first();

            if (! $profile) {
                return $this->errorResponse(
                    ErrorMessages::NOT_FOUND,
                    ApiStatuses::STATUS_NOT_FOUND
                );
            }

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make(self::ENTITY, 'show', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function update(ProfileRequest $request): JsonResponse
    {
        try {
            $profile = (new UpdateProfile)->execute($request->validated());

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make(self::ENTITY, 'update', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function storeLogo(UploadProfileLogoRequest $request): JsonResponse
    {
        try {
            $profile = (new UploadProfileLogo)->execute($request->file('logo'));

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make('Logo', 'update', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroyLogo(): JsonResponse
    {
        try {
            $profile = (new DeleteProfileLogo)->execute();

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make('Logo', 'destroy', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function storeAvatar(UploadProfileAvatarRequest $request): JsonResponse
    {
        try {
            $profile = (new UploadProfileAvatar)->execute($request->file('avatar'));

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make('Avatar', 'update', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroyAvatar(): JsonResponse
    {
        try {
            $profile = (new DeleteProfileAvatar)->execute();

            return $this->successResponse(
                new ProfileResource($profile),
                MessageHelper::make('Avatar', 'destroy', false, true)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
