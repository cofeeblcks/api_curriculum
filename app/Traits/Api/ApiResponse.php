<?php

namespace App\Traits\Api;

use App\Constants\ApiStatuses;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    protected function successResponse(
        array|Arrayable|JsonResource $data = [],
        string $message = '',
        array $meta = [],
        int $code = ApiStatuses::STATUS_OK
    ): JsonResponse {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ];

        $response = $this->cleanResponse($response);

        return response()->json($response, $code);
    }

    protected function errorResponse(
        string $message = '',
        int $code = ApiStatuses::STATUS_BAD_REQUEST,
        mixed $error = null
    ): JsonResponse {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($error instanceof ValidationException) {
            $response['errors'] = $error->errors();
        } elseif (is_array($error)) {
            $response['errors'] = $error;
        } elseif ($error instanceof \Throwable && config('app.debug')) {
            $response['error'] = $error->getMessage();
        } elseif (is_string($error) && $error !== '') {
            $response['error'] = $error;
        }

        return response()->json($response, $code);
    }

    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
            ],
        ];
    }

    private function cleanResponse(array $response): array
    {
        return array_filter(
            $response,
            fn ($value) => ! (is_array($value) && empty($value)) && $value !== '' && $value !== null
        );
    }
}
