<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\ContactMessageController;
use App\Http\Controllers\API\EducationController;
use App\Http\Controllers\API\ExperienceController;
use App\Http\Controllers\API\PortfolioController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SkillCategoryController;
use App\Http\Controllers\API\SkillController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('portfolio', [PortfolioController::class, 'show']);
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('profile/logo', [ProfileController::class, 'storeLogo']);
    Route::delete('profile/logo', [ProfileController::class, 'destroyLogo']);
    Route::post('profile/avatar', [ProfileController::class, 'storeAvatar']);
    Route::delete('profile/avatar', [ProfileController::class, 'destroyAvatar']);

    Route::apiResource('skill-categories', SkillCategoryController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('educations', EducationController::class);

    Route::get('contact-messages', [ContactMessageController::class, 'index']);
    Route::patch('contact-messages/{contactMessage}', [ContactMessageController::class, 'update']);
});
