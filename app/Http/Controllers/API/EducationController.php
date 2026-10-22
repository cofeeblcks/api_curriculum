<?php

namespace App\Http\Controllers\API;

use App\Actions\Educations\DeleteEducation;
use App\Actions\Educations\StoreEducation;
use App\Actions\Educations\UpdateEducation;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Formación';

    public function index(Request $request): JsonResponse
    {
        try {
            $educations = Education::query()->orderBy('sort_order')->get();

            return $this->successResponse(
                EducationResource::collection($educations),
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

    public function store(EducationRequest $request): JsonResponse
    {
        try {
            $education = (new StoreEducation)->execute($request->validated());

            return $this->successResponse(
                new EducationResource($education),
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

    public function show(Education $education): JsonResponse
    {
        return $this->successResponse(
            new EducationResource($education),
            MessageHelper::make(self::ENTITY, 'show', false, false)
        );
    }

    public function update(EducationRequest $request, Education $education): JsonResponse
    {
        try {
            $education = (new UpdateEducation)->execute($education, $request->validated());

            return $this->successResponse(
                new EducationResource($education),
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

    public function destroy(Education $education): JsonResponse
    {
        try {
            (new DeleteEducation)->execute($education);

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
