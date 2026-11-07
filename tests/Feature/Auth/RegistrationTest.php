<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('empty registration fields receive server-side validation errors', function () {
    $this->post('/register', [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'CampusCoin1!',
        'password_confirmation' => 'CampusCoin1!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

it('requires a name with only letters and spaces and a password with a letter number and symbol', function (string $name, string $password, string $invalidField) {
    $this->post('/register', [
        'name' => $name,
        'email' => 'test@example.com',
        'password' => $password,
        'password_confirmation' => $password,
    ])->assertSessionHasErrors($invalidField);
})->with([
    'numbers are not allowed in names' => ['Student1', 'CampusCoin1!', 'name'],
    'symbols are not allowed in names' => ['Student!', 'CampusCoin1!', 'name'],
    'passwords must contain a number' => ['Student Name', 'CampusCoin!', 'password'],
    'passwords must contain a symbol' => ['Student Name', 'CampusCoin1', 'password'],
    'spaces do not count as password symbols' => ['Student Name', 'Campus Coin1', 'password'],
    'passwords must contain a letter' => ['Student Name', '12345678!', 'password'],
]);
