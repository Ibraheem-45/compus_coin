<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('authenticated user can create and view a monthly budget', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    expect(in_array(Schema::getColumnType('budgets', 'month'), ['string', 'varchar'], true))->toBeTrue();

    $response = $this->actingAs($user)->post(route('budgets.store'), [
        'category_id' => $category->id,
        'month' => '2026-09',
        'limit_amount' => 20000,
    ]);

    $response->assertRedirect(route('budgets.index'));

    $this->assertDatabaseHas('budgets', [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'month' => '2026-09',
        'limit_amount' => '20000.00',
    ]);

    $page = $this->actingAs($user)->get(route('budgets.index'));
    $page->assertOk()->assertSee('Food');
    $this->assertSame(1, Budget::where('user_id', $user->id)->count());
});

test('students can only set budgets for expense categories', function () {
    $user = User::factory()->create();
    $incomeCategory = Category::create([
        'user_id' => $user->id,
        'name' => 'Allowance',
        'type' => 'income',
        'is_default' => false,
    ]);
    Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    $this->actingAs($user)
        ->get(route('budgets.index'))
        ->assertOk()
        ->assertSee('Food (Expense)')
        ->assertDontSee('Allowance (Income)');

    $this->actingAs($user)
        ->post(route('budgets.store'), [
            'category_id' => $incomeCategory->id,
            'month' => '2026-09',
            'limit_amount' => 2000,
        ])
        ->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('budgets', 0);
});
