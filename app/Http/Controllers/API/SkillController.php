<?php

namespace App\Http\Controllers\API;

use App\Actions\Skills\DeleteSkill;
use App\Actions\Skills\StoreSkill;
use App\Actions\Skills\UpdateSkill;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Habilidad';

    public function index(Request $request): JsonResponse
    {
        try {
            $skills = Skill::query()
                ->with('category')
                ->orderBy('sort_order')
                ->get();

            return $this->successResponse(
                SkillResource::collection($skills),
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

    public function store(SkillRequest $request): JsonResponse
    {
        try {
            $skill = (new StoreSkill)->execute($request->validated());

            return $this->successResponse(
                new SkillResource($skill),
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

    public function show(Skill $skill): JsonResponse
    {
        return $this->successResponse(
            new SkillResource($skill->load('category')),
            MessageHelper::make(self::ENTITY, 'show', false, false)
        );
    }

    public function update(SkillRequest $request, Skill $skill): JsonResponse
    {
        try {
            $skill = (new UpdateSkill)->execute($skill, $request->validated());

            return $this->successResponse(
                new SkillResource($skill),
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

    public function destroy(Skill $skill): JsonResponse
    {
        try {
            (new DeleteSkill)->execute($skill);

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
