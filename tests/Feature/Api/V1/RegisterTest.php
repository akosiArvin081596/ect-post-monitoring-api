<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function validPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'position' => 'Field Officer',
        'contact_no' => '09171234567',
    ], $overrides);
}

test('successful registration returns 201 with user and token, role is surveyor, password hashed', function () {
    $response = $this->postJson('/api/v1/register', validPayload());

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'user' => ['id', 'name', 'email', 'role', 'position', 'contact_no'],
        'token',
    ]);

    $user = User::where('email', 'jane@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('surveyor');
    expect($user->position)->toBe('Field Officer');
    expect($user->contact_no)->toBe('09171234567');
    expect($user->password)->not->toBe('secret123');
    expect(password_verify('secret123', $user->password))->toBeTrue();
});

test('duplicate email returns 422 with validation error', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/v1/register', validPayload(['email' => 'taken@example.com']));

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

test('missing name returns 422', function () {
    $payload = validPayload();
    unset($payload['name']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('missing email returns 422', function () {
    $payload = validPayload();
    unset($payload['email']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('missing password returns 422', function () {
    $payload = validPayload();
    unset($payload['password']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('missing position returns 422', function () {
    $payload = validPayload();
    unset($payload['position']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['position']);
});

test('missing contact_no returns 422', function () {
    $payload = validPayload();
    unset($payload['contact_no']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['contact_no']);
});

test('password shorter than 8 chars returns 422', function () {
    $this->postJson('/api/v1/register', validPayload([
        'password' => 'short1',
        'password_confirmation' => 'short1',
    ]))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('missing password confirmation returns 422', function () {
    $payload = validPayload();
    unset($payload['password_confirmation']);

    $this->postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('submitted role admin in payload is ignored; created user is surveyor', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['role' => 'admin']));

    $response->assertStatus(201);
    $user = User::where('email', 'jane@example.com')->first();
    expect($user->role)->toBe('surveyor');
    expect($user->isAdmin())->toBeFalse();
    expect($user->isSurveyor())->toBeTrue();
});

test('seeded admin isAdmin returns true, new registrant isSurveyor returns true', function () {
    $this->seed(AdminUserSeeder::class);
    $admin = User::where('email', 'admin@dswd.gov.ph')->first();
    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isSurveyor())->toBeFalse();

    $this->postJson('/api/v1/register', validPayload())->assertStatus(201);
    $newUser = User::where('email', 'jane@example.com')->first();
    expect($newUser->isSurveyor())->toBeTrue();
    expect($newUser->isAdmin())->toBeFalse();
});
