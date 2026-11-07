<?php

use App\Models\User;

it('student insights return empty state when there are no expense records', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $insights = app(\App\Services\StudentFinanceService::class)->insights();

    expect($insights)->toBeEmpty();
});

it('student insights refresh endpoint returns JSON for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/insights/refresh')
        ->assertOk()
        ->assertJsonIsArray();
});

it('hugginface service extracts generated text from common response formats', function () {
    config()->set('services.huggingface.token', 'fake-token');
    config()->set('services.huggingface.model', 'google/gemma-2-2b-it');

    Http::fake([
        'https://api-inference.huggingface.co/models/google/gemma-2-2b-it' => Http::response([
            [
                'generated_text' => 'This is a real AI generated insight for students.',
            ],
        ]),
    ]);

    $result = app(\App\Services\HuggingFaceInsightService::class)->generateInsight('Gifts', 5000, 'September 2026');

    expect($result)->toBe(['summary' => 'This is a real AI generated insight for students.']);
});

it('student insights use real transaction fallback data when AI insight is unavailable', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = \App\Models\Category::create([
        'user_id' => $user->id,
        'name' => 'Books',
        'type' => 'expense',
        'is_default' => false,
    ]);

    \App\Models\Transaction::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => 2500,
        'description' => 'Semester book purchase',
        'date' => now()->toDateString(),
        'recurring' => false,
        'notes' => 'Need to test fallback data output',
    ]);

    config()->set('services.huggingface.token', null);

    $insights = app(\App\Services\StudentFinanceService::class)->insights();

    expect($insights)->not->toBeEmpty()
        ->and($insights[0]['category'])->toBe('Books')
        ->and($insights[0]['explanation'])->toContain('Books')
        ->and($insights[0]['ai_generated'])->toBeFalse();
});

it('students can save and remove a transaction-based tip bookmark', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $tip = [
        'id' => 'tip_1',
        'category' => 'Food',
        'title' => 'Review Food spending',
        'description' => 'Your Food purchases total PKR 2,500 this month.',
        'potentialSavings' => 500,
    ];

    $this->postJson('/bookmarks/toggle', ['type' => 'tip', 'item' => $tip])
        ->assertOk()
        ->assertJson(['saved' => true]);

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $user->id,
        'bookmarkable_type' => 'tip',
        'bookmarkable_id' => 1,
    ]);

    $response = $this->get('/bookmarks')->assertOk();
    $response->assertSee('Review Food spending');
    $response->assertSee('Your Food purchases total PKR 2,500 this month.');

    $bookmarkId = \App\Models\Bookmark::query()->where('user_id', $user->id)->value('bookmark_id');
    $this->delete(route('student.bookmarks.destroy', $bookmarkId))->assertRedirect('/bookmarks');

    $this->assertDatabaseMissing('bookmarks', ['bookmark_id' => $bookmarkId]);
});

it('students can save a spending insight bookmark in the database', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $insight = [
        'id' => 'insight_1',
        'category' => 'Books',
        'month' => 'September 2026',
        'pattern' => 'Books is the largest expense this month.',
        'explanation' => 'Your ledger shows PKR 2,500 spent on Books.',
        'suggestedAction' => 'Set a monthly cap for Books.',
        'date' => '2026-09-26',
    ];

    $this->postJson('/bookmarks/toggle', ['type' => 'insight', 'item' => $insight])
        ->assertOk()
        ->assertJson(['saved' => true]);

    $this->assertDatabaseHas('bookmarks', [
        'user_id' => $user->id,
        'bookmarkable_type' => 'insight',
        'bookmarkable_id' => 1,
    ]);

    $this->get('/bookmarks')
        ->assertOk()
        ->assertSee('Books is the largest expense this month.')
        ->assertSee('Set a monthly cap for Books.');
});

it('students cannot view or remove another users bookmarks', function () {
    $owner = User::factory()->create();
    $otherStudent = User::factory()->create();
    $bookmark = \App\Models\Bookmark::create([
        'user_id' => $owner->id,
        'bookmarkable_type' => 'insight',
        'bookmarkable_id' => 1,
        'notes' => json_encode([
            'id' => 'insight_1',
            'category' => 'Books',
            'month' => 'September 2026',
            'pattern' => 'Books spending is increasing.',
            'explanation' => 'Your ledger shows PKR 2,500 in Books.',
            'suggestedAction' => 'Set a monthly cap.',
        ], JSON_THROW_ON_ERROR),
    ]);

    $this->actingAs($otherStudent)
        ->get('/bookmarks')
        ->assertOk()
        ->assertDontSee('Books spending is increasing.');

    $this->delete(route('student.bookmarks.destroy', $bookmark->bookmark_id))
        ->assertNotFound();

    $this->assertDatabaseHas('bookmarks', ['bookmark_id' => $bookmark->bookmark_id]);
});

it('saving tips are calculated from ledger history and include shared database templates', function () {
    \Carbon\Carbon::setTestNow('2026-09-26 12:00:00');
    $user = User::factory()->create();
    $category = \App\Models\Category::create([
        'user_id' => $user->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    foreach ([['2026-06-10', 1000], ['2026-07-10', 1000], ['2026-08-10', 1000], ['2026-09-10', 9000]] as [$date, $amount]) {
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => $amount,
            'description' => 'Campus meal',
            'date' => $date,
            'recurring' => false,
        ]);
    }

    \App\Models\Budget::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'month' => '2026-09',
        'limit_amount' => 10000,
    ]);

    \App\Models\SavingTip::create([
        'user_id' => null,
        'category' => 'Food',
        'title' => 'Use the student meal plan',
        'content' => 'The campus meal plan is a lower-cost option.',
        'impact_score' => 700,
    ]);

    $this->actingAs($user)
        ->get('/saving-tips')
        ->assertOk()
        ->assertSee('Food spending is up 800%')
        ->assertSee('Food budget is 90% used')
        ->assertSee('Use the student meal plan')
        ->assertSee('PKR 9,000');

    $this->assertDatabaseHas('saving_tips', [
        'user_id' => $user->id,
        'recommendation_key' => 'spending-2026-09-' . $category->id,
        'impact_score' => 900,
    ]);

    \Carbon\Carbon::setTestNow();
});

it('dismisses a shared tip only for the current student', function () {
    $tip = \App\Models\SavingTip::create([
        'user_id' => null,
        'category' => 'Transport',
        'title' => 'Try the campus shuttle',
        'content' => 'Compare shuttle fares with rideshare costs.',
        'impact_score' => 500,
    ]);
    $student = User::factory()->create();
    $anotherStudent = User::factory()->create();

    $this->actingAs($student)
        ->postJson(route('student.saving-tips.dismiss', $tip->tip_id))
        ->assertOk()
        ->assertJson(['dismissed' => true]);

    $this->get('/saving-tips')->assertDontSee('Try the campus shuttle');

    $this->actingAs($anotherStudent)
        ->get('/saving-tips')
        ->assertOk()
        ->assertSee('Try the campus shuttle');
});
