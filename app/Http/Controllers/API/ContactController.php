<?php

namespace App\Http\Controllers\API;

use App\Actions\Contact\StoreContactMessage;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactMessageResource;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use ApiResponse;

    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $message = (new StoreContactMessage)->execute($request->validated());

            return $this->successResponse(
                new ContactMessageResource($message),
                'Mensaje enviado correctamente',
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
}
