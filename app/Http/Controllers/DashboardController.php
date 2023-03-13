<?php

namespace App\Http\Controllers;

use App\Models\ETA\Invoice;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(Invoice $invoice)
    {
        $firstDayOfLastWeek = now()->subWeek()->toDateString();

        $today = now()->toDateString();

        $firstDayOfTheLastMonth = now()->startOfMonth()->subMonth()->toDateString();

        $lastDayOfTheLastMonth = Carbon::parse('last day of last month')->toDateString();

        $firstDayOfTheCurrentMonth = now()->startOfMonth()->toDateString();

        $lastDayOfTheCurrentMonth = now()->lastOfMonth()->toDateString();

        return Inertia::render('Dashboard/index', [
            'firstDayOfLastWeek' => $firstDayOfLastWeek,
            'today' => $today,
            'firstDayOfTheLastMonth' => $firstDayOfTheLastMonth,
            'lastDayOfTheLastMonth' => $lastDayOfTheLastMonth,
            'firstDayOfTheCurrentMonth' => $firstDayOfTheCurrentMonth,
            'lastDayOfTheCurrentMonth' => $lastDayOfTheCurrentMonth,
            'lastWeekData' => $invoice->getDashboardStatisticsByDate($firstDayOfLastWeek, $today),
            'lastMonthData' => $invoice->getDashboardStatisticsByDate($firstDayOfTheLastMonth, $lastDayOfTheLastMonth),
            'monthlyData' => $invoice->getDashboardStatisticsByDate($firstDayOfTheCurrentMonth, $lastDayOfTheCurrentMonth),
        ]);
    }
}
