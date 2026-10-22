<?php

namespace App\Http\Controllers\API;

use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            if (! Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse(
                    'Credenciales no validas.',
                    ApiStatuses::STATUS_UNAUTHORIZED
                );
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new AuthResource($user),
            ], 'Sesión iniciada');
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new AuthResource($request->user()),
            MessageHelper::make('usuario', 'show')
        );
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()?->currentAccessToken()?->delete();

            return $this->successResponse([], 'Sesión cerrada correctamente.');
        } catch (\Throwable $e) {
            return $this->errorResponse(
                'Error al cerrar la sesión.',
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }
}
