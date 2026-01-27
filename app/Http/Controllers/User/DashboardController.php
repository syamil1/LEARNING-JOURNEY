<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Mentoring;
use App\Models\EmployeeEvaluation;

class DashboardController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;

        $totalSpv = Employee::where('store_id', $store->id)->count();

        $onboardingPending = Employee::where('store_id', $store->id)
            ->whereDoesntHave('onboardingChecklists', fn ($q) =>
                $q->whereIn('month', [0])
            )
            ->whereHas('onboardingChecklists', fn ($q) =>
                $q->whereBetween('month', [1, 6])
                  ->where('status', '!=', 'approved')
            )
            ->count();

        $mentoringActive = Mentoring::whereHas('employee', fn ($q) =>
            $q->where('store_id', $store->id)
        )->where('status', 'active')->count();

        $developmentPending = Employee::where('store_id', $store->id)
            ->whereDoesntHave('developmentScore')
            ->count();

        $supervisors = EmployeeEvaluation::with([
            'employee.introduction',
            'employee.onboardingChecklists',
            'employee.mentorings',
            'employee.developmentScore',
        ])
        ->whereHas('employee', fn ($q) =>
            $q->where('store_id', $store->id)
        )
        ->get()
        ->map(function ($ev) {

            $emp = $ev->employee;

            $intro = $emp->introduction;

            $ev->intro_status = $intro &&
                !(
                    $intro->fgd_analytic_score == 0 &&
                    $intro->fgd_business_score == 0 &&
                    $intro->fgd_leadership_score == 0 &&
                    $intro->interview_analytic_score == 0 &&
                    $intro->interview_business_score == 0 &&
                    $intro->interview_leadership_score == 0
                )
                ? 'Sudah'
                : 'Belum';

            $checklists = $emp->onboardingChecklists;

            if ($checklists->contains(fn ($c) => $c->month == 0)) {
                $ev->checklist_summary = '6/6';
            } else {
                $approvedMonths = 0;

                $grouped = $checklists
                    ->whereBetween('month', [1, 6])
                    ->groupBy('month');

                foreach ($grouped as $weeks) {
                    if (
                        $weeks->count() === 4 &&
                        $weeks->every(fn ($w) => $w->status === 'approved')
                    ) {
                        $approvedMonths++;
                    }
                }

                $ev->checklist_summary = $approvedMonths . '/6';
            }

            $ev->total_mentoring = $emp->mentorings
                ->where('status', 'verified')
                ->count();

            $ev->avg_development = $emp->developmentScore
                ? $emp->developmentScore->rso_average
                : null;

            return $ev;
        });

        return view('user.dashboard', compact(
            'totalSpv',
            'onboardingPending',
            'mentoringActive',
            'developmentPending',
            'supervisors'
        ));
    }
}
