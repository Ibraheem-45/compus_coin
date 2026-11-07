<?php

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

function createReportTransaction(User $user, Category $category, string $type, float $amount, string $date): Transaction
{
    return Transaction::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => $type,
        'amount' => $amount,
        'description' => $category->name . ' report test',
        'date' => $date,
        'recurring' => false,
    ]);
}

it('reports only the current students real records across the previous six months', function () {
    Carbon::setTestNow('2026-09-26 12:00:00');
    $student = User::factory()->create();
    $otherStudent = User::factory()->create();
    $food = Category::create(['user_id' => $student->id, 'name' => 'Food', 'type' => 'expense', 'is_default' => false]);
    $income = Category::create(['user_id' => $student->id, 'name' => 'Allowance', 'type' => 'income', 'is_default' => false]);
    $otherFood = Category::create(['user_id' => $otherStudent->id, 'name' => 'Other Food', 'type' => 'expense', 'is_default' => false]);

    createReportTransaction($student, $food, 'expense', 1000, '2026-09-01');
    createReportTransaction($student, $food, 'expense', 1500, '2026-09-18');
    createReportTransaction($student, $food, 'expense', 700, '2026-08-20');
    createReportTransaction($student, $income, 'income', 5000, '2026-09-19');
    createReportTransaction($otherStudent, $otherFood, 'expense', 9000, '2026-09-10');

    $response = $this->actingAs($student)->getJson('/reports/data')->assertOk();
    $data = $response->json();

    expect($data['labels'])->toHaveCount(6)
        ->and($data['labels'][0])->toBe('Apr 2026')
        ->and($data['monthly_expense'][5])->toBe(2500)
        ->and($data['monthly_income'][5])->toBe(5000)
        ->and($data['daily_labels'])->toBe(['01 Sep', '18 Sep'])
        ->and($data['daily_spend'])->toBe([1000, 1500])
        ->and($data['weekly_spend'])->toBe([1000, 0, 1500, 0, 0])
        ->and($data['category_labels'])->toBe(['Food'])
        ->and($data['category_values'])->toBe([3200]);

    Carbon::setTestNow();
});

it('renders report filters using the signed in students available categories', function () {
    Carbon::setTestNow('2026-09-26 12:00:00');
    $student = User::factory()->create();
    Category::create(['user_id' => $student->id, 'name' => 'Student Food', 'type' => 'expense', 'is_default' => false]);

    $this->actingAs($student)
        ->get('/reports')
        ->assertOk()
        ->assertSee('reportDateFrom')
        ->assertSee('reportDateTo')
        ->assertSee('Student Food (Expense)')
        ->assertSee('reportFlowFilter')
        ->assertSee('reportData');

    Carbon::setTestNow();
});

it('filters reports by date range category and transaction flow', function () {
    Carbon::setTestNow('2026-09-26 12:00:00');
    $student = User::factory()->create();
    $food = Category::create(['user_id' => $student->id, 'name' => 'Food', 'type' => 'expense', 'is_default' => false]);
    $transport = Category::create(['user_id' => $student->id, 'name' => 'Transport', 'type' => 'expense', 'is_default' => false]);
    $income = Category::create(['user_id' => $student->id, 'name' => 'Allowance', 'type' => 'income', 'is_default' => false]);

    createReportTransaction($student, $food, 'expense', 1200, '2026-09-05');
    createReportTransaction($student, $food, 'expense', 800, '2026-09-25');
    createReportTransaction($student, $transport, 'expense', 600, '2026-09-15');
    createReportTransaction($student, $income, 'income', 7000, '2026-09-10');

    $this->actingAs($student)
        ->getJson('/reports/data?date_from=2026-09-01&date_to=2026-09-20&category_id=' . $food->id . '&flow=expense')
        ->assertOk()
        ->assertJsonPath('monthly_expense', [1200])
        ->assertJsonPath('monthly_income', [0])
        ->assertJsonPath('category_labels', ['Food'])
        ->assertJsonPath('category_values', [1200]);

    Carbon::setTestNow();
});

it('does not let a student filter reports using another students category', function () {
    $student = User::factory()->create();
    $otherStudent = User::factory()->create();
    $category = Category::create(['user_id' => $otherStudent->id, 'name' => 'Private category', 'type' => 'expense', 'is_default' => false]);

    $this->actingAs($student)
        ->getJson('/reports/data?category_id=' . $category->id)
        ->assertNotFound();
});