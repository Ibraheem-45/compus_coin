<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.master', function ($view): void {
            $view->with([
                'adminSidebarStudentCount' => User::where('role', 'student')->count(),
                'adminSidebarCategoryCount' => Category::where('is_default', true)->count(),
            ]);
        });
    }
}
