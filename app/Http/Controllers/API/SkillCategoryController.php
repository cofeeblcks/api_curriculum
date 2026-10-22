<?php

namespace App\Http\Controllers\API;

use App\Actions\Skills\DeleteSkillCategory;
use App\Actions\Skills\StoreSkillCategory;
use App\Actions\Skills\UpdateSkillCategory;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkillCategoryRequest;
use App\Http\Resources\SkillCategoryResource;
use App\Models\SkillCategory;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillCategoryController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Categoría';

    public function index(Request $request): JsonResponse
    {
        try {
            $categories = SkillCategory::query()
                ->with('skills')
                ->orderBy('sort_order')
                ->get();

            return $this->successResponse(
                SkillCategoryResource::collection($categories),
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

    public function store(SkillCategoryRequest $request): JsonResponse
    {
        try {
            $category = (new StoreSkillCategory)->execute($request->validated());

            return $this->successResponse(
                new SkillCategoryResource($category),
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

    public function show(SkillCategory $skillCategory): JsonResponse
    {
        return $this->successResponse(
            new SkillCategoryResource($skillCategory->load('skills')),
            MessageHelper::make(self::ENTITY, 'show', false, false)
        );
    }

    public function update(SkillCategoryRequest $request, SkillCategory $skillCategory): JsonResponse
    {
        try {
            $category = (new UpdateSkillCategory)->execute($skillCategory, $request->validated());

            return $this->successResponse(
                new SkillCategoryResource($category),
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

    public function destroy(SkillCategory $skillCategory): JsonResponse
    {
        try {
            (new DeleteSkillCategory)->execute($skillCategory);

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
