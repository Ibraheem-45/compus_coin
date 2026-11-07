<?php

use App\Models\Budget;
use App\Models\CampusAnnouncement;
use App\Models\Category;
use App\Models\StudentNotification;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

it('shows database campus announcements on the student dashboard and notifications page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $student = User::factory()->create();

    $announcement = $this->actingAs($admin)
        ->postJson('/admin/announcements', [
            'title' => 'Library hours extended',
            'message' => 'The campus library stays open until midnight during exams.',
            'priority' => 'High',
            'office' => 'Student Affairs',
        ])
        ->assertCreated()
        ->json();

    $this->assertDatabaseHas('campus_announcements', [
        'id' => $announcement['id'],
        'posted_by' => $admin->id,
    ]);

    $this->actingAs($student)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Library hours extended')
        ->assertSee('The campus library stays open until midnight during exams.');

    $this->get('/notifications')
        ->assertOk()
        ->assertSee('Library hours extended')
        ->assertSee('The campus library stays open until midnight during exams.');

    $this->assertDatabaseHas('student_notifications', [
        'user_id' => $student->id,
        'source_type' => 'announcement',
        'source_key' => 'announcement:' . $announcement['id'],
    ]);
});

it('creates budget alerts from current student expenses and stores read state per user', function () {
    Carbon::setTestNow('2026-09-26 12:00:00');
    $student = User::factory()->create();
    $otherStudent = User::factory()->create();
    $category = Category::create([
        'user_id' => $student->id,
        'name' => 'Food',
        'type' => 'expense',
        'is_default' => false,
    ]);

    Budget::create([
        'user_id' => $student->id,
        'category_id' => $category->id,
        'month' => '2026-09',
        'limit_amount' => 10000,
    ]);

    Transaction::create([
        'user_id' => $student->id,
        'category_id' => $category->id,
        'type' => 'expense',
        'amount' => 8500,
        'description' => 'Campus meals',
        'date' => '2026-09-20',
        'recurring' => false,
    ]);

    $this->actingAs($student)
        ->get('/notifications')
        ->assertOk()
        ->assertSee('Food budget nearly reached')
        ->assertSee('PKR 8,500 of your PKR 10,000 Food budget');

    $notification = StudentNotification::query()
        ->where('user_id', $student->id)
        ->where('source_type', 'budget')
        ->firstOrFail();

    $this->postJson('/notifications/' . $notification->id . '/read')
        ->assertOk()
        ->assertJson(['read' => true]);

    expect($notification->fresh()->read_at)->not->toBeNull();

    $this->actingAs($otherStudent)
        ->postJson('/notifications/' . $notification->id . '/read')
        ->assertNotFound();

    Carbon::setTestNow();
});

it('does not show hardcoded notifications when there are no database notifications', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get('/notifications')
        ->assertOk()
        ->assertSee('No notifications yet.')
        ->assertDontSee('Budget Limit Exceeded')
        ->assertDontSee('Budget Near Limit');
});
