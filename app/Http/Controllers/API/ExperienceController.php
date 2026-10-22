<?php

namespace App\Http\Controllers\API;

use App\Actions\Experiences\DeleteExperience;
use App\Actions\Experiences\StoreExperience;
use App\Actions\Experiences\UpdateExperience;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Experiencia';

    public function index(Request $request): JsonResponse
    {
        try {
            $experiences = Experience::query()->orderBy('sort_order')->get();

            return $this->successResponse(
                ExperienceResource::collection($experiences),
                MessageHelper::make(self::ENTITY, 'index', true, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function store(ExperienceRequest $request): JsonResponse
    {
        try {
            $experience = (new StoreExperience)->execute($request->validated());

            return $this->successResponse(
                new ExperienceResource($experience),
                MessageHelper::make(self::ENTITY, 'store', false, false),
                [],
                ApiStatuses::STATUS_CREATED
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function show(Experience $experience): JsonResponse
    {
        return $this->successResponse(
            new ExperienceResource($experience),
            MessageHelper::make(self::ENTITY, 'show', false, false)
        );
    }

    public function update(ExperienceRequest $request, Experience $experience): JsonResponse
    {
        try {
            $experience = (new UpdateExperience)->execute($experience, $request->validated());

            return $this->successResponse(
                new ExperienceResource($experience),
                MessageHelper::make(self::ENTITY, 'update', false, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroy(Experience $experience): JsonResponse
    {
        try {
            (new DeleteExperience)->execute($experience);

            return $this->successResponse(
                [],
                MessageHelper::make(self::ENTITY, 'destroy', false, false)
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
