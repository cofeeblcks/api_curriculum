<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create());
});

it('creates and lists experiences', function () {
    $create = $this->postJson('/api/experiences', [
        'title' => 'Backend Developer',
        'company' => 'CoffeeBlcks',
        'location' => 'Remoto',
        'startsOn' => '2024-01-01',
        'endsOn' => null,
        'description' => 'Desarrollo de APIs Laravel.',
        'techTags' => ['Laravel', 'MySQL'],
        'sortOrder' => 1,
    ]);

    $create->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.title', 'Backend Developer')
        ->assertJsonPath('data.techTags.0', 'Laravel');

    $this->getJson('/api/experiences')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(1, 'data');
});

it('updates and deletes an experience', function () {
    $created = $this->postJson('/api/experiences', [
        'title' => 'Dev',
        'company' => 'Acme',
        'startsOn' => '2020-01-01',
        'sortOrder' => 1,
    ])->json('data');

    $this->putJson('/api/experiences/'.$created['id'], [
        'title' => 'Senior Dev',
        'company' => 'Acme',
        'startsOn' => '2020-01-01',
        'sortOrder' => 1,
    ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Senior Dev');

    $this->deleteJson('/api/experiences/'.$created['id'])
        ->assertOk()
        ->assertJsonPath('status', 'success');
});
