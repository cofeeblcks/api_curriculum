<?php

namespace App\Http\Controllers\API;

use App\Actions\Portfolio\GetPublicPortfolio;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioResource;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    use ApiResponse;

    public function show(): JsonResponse
    {
        try {
            $portfolio = (new GetPublicPortfolio)->execute();

            if ($portfolio['profile'] === null) {
                return $this->errorResponse(
                    'Perfil no configurado.',
                    ApiStatuses::STATUS_NOT_FOUND
                );
            }

            return $this->successResponse(
                new PortfolioResource($portfolio),
                'Portfolio obtenido correctamente'
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
