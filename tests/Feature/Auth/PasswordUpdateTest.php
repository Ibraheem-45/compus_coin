<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('settings page posts password changes to the authenticated update route', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertOk()
        ->assertSee('action="' . route('password.update') . '"', false)
        ->assertSee('name="current_password"', false)
        ->assertSee('name="password_confirmation"', false)
        ->assertSee('placeholder="Enter your current password"', false)
        ->assertSee('placeholder="Re-enter your new password"', false)
        ->assertSee('password-visibility-toggle', false)
        ->assertDontSee('â€¢');
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});
