<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentFinanceService;
use Illuminate\Http\JsonResponse;

class InsightController extends Controller
{
    public function __construct(protected StudentFinanceService $finance)
    {
    }

    public function refresh(): JsonResponse
    {
        return response()->json($this->finance->insights());
    }
}