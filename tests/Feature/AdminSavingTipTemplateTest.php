<?php

use App\Models\SavingTip;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('requires an admin role to manage shared saving tip templates', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get('/admin/tip-templates')
        ->assertForbidden();

    $this->actingAs($student)
        ->postJson('/admin/tip-templates', [
            'title' => 'Use the campus shuttle',
            'category' => 'Transport',
            'impact_score' => 500,
            'content' => 'Compare shuttle fares with rideshare costs.',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('saving_tips', 0);
});

it('allows admins to create edit list and delete shared saving tip templates', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $created = $this->actingAs($admin)
        ->postJson('/admin/tip-templates', [
            'title' => 'Use the campus shuttle',
            'category' => 'Transport',
            'impact_score' => 500,
            'content' => 'Compare shuttle fares with rideshare costs.',
        ])
        ->assertCreated()
        ->assertJsonPath('title', 'Use the campus shuttle')
        ->assertJsonPath('saving', 500)
        ->json();

    $tip = SavingTip::findOrFail($created['id']);
    expect($tip->user_id)->toBeNull();

    $this->getJson('/admin/tip-templates')
        ->assertOk()
        ->assertJsonFragment(['title' => 'Use the campus shuttle']);

    $this->putJson('/admin/tip-templates/' . $tip->tip_id, [
        'title' => 'Choose the campus shuttle',
        'category' => 'Transport',
        'impact_score' => 750,
        'content' => 'Use the shuttle to lower monthly commute costs.',
    ])
        ->assertOk()
        ->assertJsonPath('title', 'Choose the campus shuttle')
        ->assertJsonPath('saving', 750);

    $this->deleteJson('/admin/tip-templates/' . $tip->tip_id)
        ->assertOk()
        ->assertJson(['deleted' => true]);

    $this->assertDatabaseMissing('saving_tips', ['tip_id' => $tip->tip_id]);
});

it('routes an authenticated admin login to the admin dashboard', function () {
    $admin = User::factory()->create([
        'email' => 'admin@campuscoin.test',
        'password' => Hash::make('secret-password'),
        'role' => 'admin',
    ]);

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('admin.dashboard'));
});

it('renders the home page for an authenticated admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertSee(route('admin.dashboard'));
});

it('shows server-side validation errors for empty login fields', function () {
    $this->post('/login', [])
        ->assertSessionHasErrors(['email', 'password']);
});

it('renders the database-backed tip template editor for admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/announcements')
        ->assertOk()
        ->assertSee('Saving Tip Templates')
        ->assertSee('addTipTemplateModal')
        ->assertSee('tipTemplatesTbody');
});