<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CampusAnnouncement;
use App\Services\StudentFinanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected StudentFinanceService $finance)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $data = $this->finance->dashboardData();

        return view('student.dashboard', [
            'user' => $user,
            'stats' => $data['stats'],
            'dashboardData' => $data['dashboardData'],
            'announcements' => CampusAnnouncement::query()
                ->where('is_active', true)
                ->latest()
                ->take(3)
                ->get(),
        ]);
    }
}
