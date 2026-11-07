<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

test('authenticated user can create and view a real transaction', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 250,
            'description' => 'Lunch at cafeteria',
            'date' => '2026-09-24',
            'recurring' => false,
            'recurring_frequency' => null,
            'notes' => 'Late lunch',
        ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'description' => 'Lunch at cafeteria',
        'type' => 'expense',
    ]);

    $page = $this->actingAs($user)->get(route('transactions.index'));
    $page->assertOk()->assertSee('Lunch at cafeteria');
    $this->assertSame(1, Transaction::where('user_id', $user->id)->count());
});

test('quick add preselects income and only accepts matching categories', function () {
    $user = User::factory()->create();
    $incomeCategory = Category::create([
        'user_id' => $user->id,
        'name' => 'Allowance',
        'type' => 'income',
        'is_default' => false,
    ]);
    $expenseCategory = Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    $this->actingAs($user)
        ->get(route('transactions.create', ['type' => 'income']))
        ->assertOk()
        ->assertSee('value="income" checked', false);

    $this->actingAs($user)
        ->post(route('transactions.store'), [
            'category_id' => $expenseCategory->id,
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Monthly allowance',
            'date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors('category_id');

    $this->assertDatabaseMissing('transactions', ['category_id' => $expenseCategory->id]);
    $this->assertDatabaseMissing('transactions', ['category_id' => $incomeCategory->id]);
});
