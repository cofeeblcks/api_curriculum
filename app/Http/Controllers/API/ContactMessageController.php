<?php

namespace App\Http\Controllers\API;

use App\Actions\Contact\MarkContactMessageRead;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageUpdateRequest;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Mensaje';

    public function index(Request $request): JsonResponse
    {
        try {
            $messages = ContactMessage::query()
                ->orderByDesc('created_at')
                ->paginate($request->integer('per_page', 15));

            return $this->successResponse(
                ContactMessageResource::collection($messages),
                MessageHelper::make(self::ENTITY, 'index', true, true),
                $this->paginationMeta($messages)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function update(ContactMessageUpdateRequest $request, ContactMessage $contactMessage): JsonResponse
    {
        try {
            $message = (new MarkContactMessageRead)->execute(
                $contactMessage,
                $request->boolean('read')
            );

            return $this->successResponse(
                new ContactMessageResource($message),
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
}
