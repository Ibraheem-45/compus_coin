<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\InsightController;
use App\Http\Controllers\Student\BookmarkController;
use App\Http\Controllers\Student\CsvImportController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\SavingTipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SavingTipTemplateController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/', function () {
    return view('home');
})->name('home');




// Student Routes


Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/account/status', fn () => response()->noContent())
        ->name('account.status');

    Route::get('/profile', function () {
        return view('student.profile', ['user' => auth()->user()]);
    })->name('profile.edit');


    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('transactions', \App\Http\Controllers\TransactionController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::get('/add-transaction', function () {
        return redirect()->route('transactions.create');
    })->name('student.add-transaction');

    Route::get('/edit-transaction', function () {
        return redirect()->route('transactions.index');
    })->name('student.edit-transaction');

    Route::resource('budgets', \App\Http\Controllers\BudgetController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/reports', [ReportController::class, 'index'])->name('student.reports');
    Route::get('/reports/data', [ReportController::class, 'data'])->name('student.reports.data');
    Route::get('/insights/refresh', [InsightController::class, 'refresh'])->name('student.insights.refresh');
    Route::get('/saving-tips', [SavingTipController::class, 'index'])->name('student.saving-tips');
    Route::post('/saving-tips/{tip}/dismiss', [SavingTipController::class, 'dismiss'])->name('student.saving-tips.dismiss');

    Route::resource('categories', \App\Http\Controllers\CategoryController::class)
    ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('student.bookmarks');
    Route::post('/bookmarks/toggle', [BookmarkController::class, 'toggle'])->name('student.bookmarks.toggle');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('student.bookmarks.destroy');

    Route::get('/csv-import', [CsvImportController::class, 'index'])->name('student.csv-import');
    Route::post('/csv-import', [CsvImportController::class, 'store'])->name('student.csv-import.store');
    Route::get('/csv-import/template', [CsvImportController::class, 'template'])->name('student.csv-import.template');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('student.notifications');
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('student.notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('student.notifications.read-all');

    Route::get('/settings', function () {
        return view('student.settings');
    })->name('student.settings');
});




        // Admin Routes


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('admin.admin-login');
    })->name('login');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])
            ->name('analytics');

        Route::prefix('announcements')->controller(AnnouncementController::class)->group(function () {
            Route::get('/', 'index')->name('announcements');

            Route::get('/list', 'list')->name('announcements.list');

            Route::post('/', 'store')->name('announcements.store');

            Route::delete('/{announcement}', 'destroy')->name('announcements.destroy');
        });

        Route::prefix('tip-templates')
            ->name('tip-templates.')
            ->controller(SavingTipTemplateController::class)
            ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{savingTip}', 'update')->name('update');
            Route::delete('/{savingTip}', 'destroy')->name('destroy');
            });

        Route::middleware(['auth', 'admin'])->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])
                ->name('users');

            Route::get('/users/{user}', [AdminUserController::class, 'show'])
                ->name('users.show');

            Route::put('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
                ->name('users.reset-password');

            Route::patch('/users/{user}/disable', [AdminUserController::class, 'disable'])
                ->name('users.disable');

            Route::patch('/users/{user}/enable', [AdminUserController::class, 'enable'])
                ->name('users.enable');
        });

        Route::get('/audit', function () {
            return view('admin.admin-audit');
        })->name('audit');

        Route::get('/categories', [AdminCategoryController::class, 'index'])
            ->name('categories');

        Route::get('/categories/create', [AdminCategoryController::class, 'create'])
            ->name('categories.create');

        Route::post('/categories', [AdminCategoryController::class, 'store'])
            ->name('categories.store');

        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])
            ->name('categories.update');

        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])
            ->name('categories.destroy');
    });
});



require __DIR__ . '/auth.php';

// One-click live database initialization & migration
Route::get('/setup-db', function () {
    try {
        $dbPath = database_path('database.sqlite');
        if (!\Illuminate\Support\Facades\File::exists($dbPath)) {
            \Illuminate\Support\Facades\File::put($dbPath, '');
        }
        \Illuminate\Support\Facades\Artisan::call('migrate --force');
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        \Illuminate\Support\Facades\Artisan::call('db:seed --force');
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Database successfully migrated and seeded with admin & student demo data!',
            'migrate' => $migrateOutput,
            'seed' => $seedOutput,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ], 500);
    }
});

