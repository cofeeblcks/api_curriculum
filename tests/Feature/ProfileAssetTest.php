<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Storage::fake('public');
    Sanctum::actingAs(User::factory()->create());
    Profile::query()->create(['name' => 'Test', 'available' => true]);
});

it('uploads a logo and returns logoUrl', function () {
    $file = UploadedFile::fake()->create('logo.png', 100, 'image/png');

    $this->post('/api/profile/logo', ['logo' => $file], ['Accept' => 'application/json'])
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.logoUrl', fn ($url) => is_string($url) && str_contains($url, '/storage/'));

    Storage::disk('public')->assertExists(
        Profile::query()->first()->logo_path
    );
});

it('uploads an avatar and returns avatarUrl', function () {
    $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

    $this->post('/api/profile/avatar', ['avatar' => $file], ['Accept' => 'application/json'])
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.avatarUrl', fn ($url) => is_string($url) && str_contains($url, '/storage/'));

    Storage::disk('public')->assertExists(
        Profile::query()->first()->avatar_path
    );
});

it('deletes the logo and clears logoUrl', function () {
    $file = UploadedFile::fake()->create('logo.png', 100, 'image/png');

    $this->post('/api/profile/logo', ['logo' => $file], ['Accept' => 'application/json'])
        ->assertOk();

    $path = Profile::query()->first()->logo_path;
    Storage::disk('public')->assertExists($path);

    $this->deleteJson('/api/profile/logo')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.logoUrl', null);

    expect(Profile::query()->first()->logo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('deletes the avatar and clears avatarUrl', function () {
    $file = UploadedFile::fake()->create('avatar.png', 100, 'image/png');

    $this->post('/api/profile/avatar', ['avatar' => $file], ['Accept' => 'application/json'])
        ->assertOk();

    $path = Profile::query()->first()->avatar_path;
    Storage::disk('public')->assertExists($path);

    $this->deleteJson('/api/profile/avatar')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.avatarUrl', null);

    expect(Profile::query()->first()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('rejects invalid logo uploads with 422', function () {
    $file = UploadedFile::fake()->create('logo.pdf', 100, 'application/pdf');

    $this->post('/api/profile/logo', ['logo' => $file], ['Accept' => 'application/json'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['logo']);
});

it('rejects invalid avatar uploads with 422', function () {
    $file = UploadedFile::fake()->create('avatar.gif', 100, 'image/gif');

    $this->post('/api/profile/avatar', ['avatar' => $file], ['Accept' => 'application/json'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar']);
});

it('replaces previous logo file on re-upload', function () {
    $first = UploadedFile::fake()->create('logo-a.png', 100, 'image/png');
    $this->post('/api/profile/logo', ['logo' => $first], ['Accept' => 'application/json'])
        ->assertOk();

    $oldPath = Profile::query()->first()->logo_path;

    $second = UploadedFile::fake()->create('logo-b.png', 100, 'image/png');
    $this->post('/api/profile/logo', ['logo' => $second], ['Accept' => 'application/json'])
        ->assertOk();

    $newPath = Profile::query()->first()->logo_path;

    expect($newPath)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
});
