<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
    ]);
});

it('logs in with valid credentials', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonStructure(['data' => ['access_token', 'user' => ['id', 'name', 'email']]]);
});

it('rejects invalid credentials', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized()
        ->assertJsonPath('status', 'error');
});

it('returns the authenticated user', function () {
    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
    Sanctum::actingAs($user);

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.email', 'admin@example.com');
});

it('logs out and revokes the current token', function () {
    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/auth/logout')
        ->assertOk()
        ->assertJsonPath('status', 'success');
});
