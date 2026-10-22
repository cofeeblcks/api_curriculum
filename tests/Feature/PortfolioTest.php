<?php

use Database\Seeders\CurriculumSeeder;

it('returns the public portfolio', function () {
    $this->seed(CurriculumSeeder::class);

    $response = $this->getJson('/api/portfolio');

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.profile.name', 'Hadik A. Chavez V.')
        ->assertJsonStructure([
            'data' => [
                'profile' => ['name', 'headline', 'bio', 'yearsExperience', 'available'],
                'skillCategories',
                'experiences',
                'educations',
            ],
        ]);
});

it('stores a contact message', function () {
    $response = $this->postJson('/api/contact', [
        'name' => 'Cliente Demo',
        'email' => 'cliente@example.com',
        'message' => 'Hola, me interesa un proyecto.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.email', 'cliente@example.com');

    $this->assertDatabaseHas('contact_messages', [
        'email' => 'cliente@example.com',
    ]);
});
