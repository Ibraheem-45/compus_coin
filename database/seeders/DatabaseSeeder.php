<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SavingTip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ─── Admin Account ───────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@campuscoin.local'],
            [
                'name'              => 'Campus Admin',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'admin',
            ]
        );

        // ─── Demo Student Account ─────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'student@campuscoin.local'],
            [
                'name'                     => 'Ibraheem Demo',
                'email_verified_at'        => now(),
                'password'                 => Hash::make('password'),
                'role'                     => 'student',
                'academic_year'            => '2nd Year',
                'monthly_allowance_baseline' => 15000,
                'monthly_saving_goal'      => 3000,
            ]
        );

        // ─── Default Global Categories ────────────────────────────────
        $defaultCategories = [
            // Income
            ['name' => 'Monthly Allowance',  'type' => 'income'],
            ['name' => 'Part-time Job',       'type' => 'income'],
            ['name' => 'Scholarship',         'type' => 'income'],
            ['name' => 'Gift / Eid Money',    'type' => 'income'],
            ['name' => 'Freelance',           'type' => 'income'],
            ['name' => 'Other Income',        'type' => 'income'],
            // Expense
            ['name' => 'Food & Dining',       'type' => 'expense'],
            ['name' => 'Transport / Rickshaw','type' => 'expense'],
            ['name' => 'Hostel / Rent',       'type' => 'expense'],
            ['name' => 'Books & Stationery',  'type' => 'expense'],
            ['name' => 'Lab / Exam Fee',      'type' => 'expense'],
            ['name' => 'Subscriptions',       'type' => 'expense'],
            ['name' => 'Mobile / Internet',   'type' => 'expense'],
            ['name' => 'Entertainment',       'type' => 'expense'],
            ['name' => 'Clothing',            'type' => 'expense'],
            ['name' => 'Medical',             'type' => 'expense'],
            ['name' => 'Miscellaneous',       'type' => 'expense'],
        ];

        foreach ($defaultCategories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                ['user_id' => null, 'is_default' => true]
            );
        }

        // ─── Saving Tip Templates (Campus-Wide Library) ───────────────
        $tips = [
            [
                'title'        => 'Cook Weekend Meals in Hostel',
                'category'     => 'Food & Dining',
                'impact_score' => 2500,
                'content'      => 'Buying groceries and cooking with 2–3 roommates on weekends can cut your monthly food expense by up to PKR 2,500. Split the cost and cook desi meals instead of ordering takeaway.',
            ],
            [
                'title'        => 'Use University Shuttle Instead of Uber',
                'category'     => 'Transport',
                'impact_score' => 1800,
                'content'      => 'University buses and vans are often free or heavily subsidised. Switching from daily Uber/Careem rides to campus transport can save PKR 1,800+ per month easily.',
            ],
            [
                'title'        => 'Borrow Textbooks from Library',
                'category'     => 'Books & Stationery',
                'impact_score' => 3000,
                'content'      => 'Most textbooks are available in the university library or can be borrowed from seniors. Avoid buying new books each semester — this alone can save PKR 2,000–4,000 per term.',
            ],
            [
                'title'        => 'Share Netflix / Streaming with Roommates',
                'category'     => 'Subscriptions',
                'impact_score' => 900,
                'content'      => 'A family/group plan split between 4 students costs only PKR 150–250 per person instead of PKR 1,100 each. Use screen sharing for music and video platforms too.',
            ],
            [
                'title'        => 'Set a Daily Canteen Budget Limit',
                'category'     => 'Food & Dining',
                'impact_score' => 1500,
                'content'      => 'Students who set a daily canteen limit of PKR 200–250 instead of spending spontaneously save an average of PKR 1,500 per month. Use the Campus Coin daily tracker to enforce this.',
            ],
            [
                'title'        => 'Apply for University Scholarship / Financial Aid',
                'category'     => 'Income',
                'impact_score' => 10000,
                'content'      => 'Most universities offer need-based and merit-based financial aid. Even a partial scholarship of PKR 5,000–15,000/month can dramatically reduce financial stress. Visit the student affairs office.',
            ],
        ];

        foreach ($tips as $tip) {
            SavingTip::firstOrCreate(
                ['title' => $tip['title'], 'user_id' => null],
                [
                    'category'     => $tip['category'],
                    'impact_score' => $tip['impact_score'],
                    'content'      => $tip['content'],
                    'user_id'      => null,
                ]
            );
        }
    }
}
