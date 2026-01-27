<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChecklistTemplate;
use App\Models\OnboardingChecklist;
use App\Models\Employee;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    public function index()
    {
        $store = auth()->user()->store;

        $templates = ChecklistTemplate::all()->groupBy('month');

        $employees = Employee::where('store_id', $store->id)
            ->with('onboardingChecklists')
            ->get()
            ->map(function ($employee) use ($templates) {

                $hasFull = $employee->onboardingChecklists
                    ->contains(fn($c) => $c->month == 0);

                if ($hasFull) {
                    $employee->months = $templates->map(function ($weeks, $month) {
                        return [
                            'month'  => $month,
                            'status' => 'approved',
                            'weeks'  => $weeks->map(fn ($w) => [
                                'week'   => $w->week,
                                'filled' => true,
                            ]),
                        ];
                    })->values();

                    $employee->is_full = true;
                    return $employee;
                }

                $months = [];

                foreach ($templates as $month => $weeks) {

                    $filled = $employee->onboardingChecklists->where('month', $month);

                    if ($filled->contains(fn($i) => $i->status === 'rejected')) {
                        $status = 'rejected';
                    } else {
                        $totalWeeks = $weeks->count();
                        $completedWeeks = $filled->where('status', 'approved')->count();

                        $status = match (true) {
                            $filled->isEmpty() => 'not_started',
                            $completedWeeks < $totalWeeks => 'in_progress',
                            default => 'approved',
                        };
                    }

                    $months[] = [
                        'month'  => $month,
                        'status' => $status,
                        'weeks'  => $weeks->map(fn ($w) => [
                            'week'   => $w->week,
                            'filled' => $filled->where('week', $w->week)->isNotEmpty(),
                        ]),
                    ];
                }

                $employee->months = $months;
                return $employee;
            });

        return view('user.onboardingchecklist.index', compact('employees'));
    }

    public function show($employeeId, $month, $week)
    {
        $hasFull = OnboardingChecklist::where('employee_id', $employeeId)
            ->where('month', 0)
            ->exists();

        if ($hasFull) {
            return redirect()
                ->route('user.onboarding.checklist.index')
                ->with('info', 'Onboarding employee ini sudah auto approved.');
        }

        $checklist = OnboardingChecklist::where([
            'employee_id' => $employeeId,
            'month'       => $month,
            'week'        => $week,
        ])->first();

        if (!$checklist) {
            $template = ChecklistTemplate::where('month', $month)
                ->where('week', $week)
                ->firstOrFail();

            $checklist = OnboardingChecklist::create([
                'employee_id'    => $employeeId,
                'month'          => $month,
                'week'           => $week,
                'checklist_json' => $template->template_json,
                'status'         => 'pending',
            ]);
        }

        $data = $checklist->checklist_json;

        return view('user.onboardingchecklist.show', compact(
            'checklist',
            'data',
            'month',
            'week'
        ));
    }

    public function store(Request $request, $employeeId, $month, $week)
    {
        $hasFull = OnboardingChecklist::where('employee_id', $employeeId)
            ->where('month', 0)
            ->exists();

        if ($hasFull) {
            return redirect()
                ->route('user.onboarding.checklist.index')
                ->with('info', 'Onboarding employee ini sudah auto approved.');
        }

        OnboardingChecklist::where([
            'employee_id' => $employeeId,
            'month'       => $month,
            'week'        => $week,
        ])->update([
            'checklist_json'      => $request->input('checklist'),
            'filled_by'           => $request->filled_by,
            'notes_store_manager' => $request->notes_store_manager,
        ]);

        OnboardingChecklist::where('employee_id', $employeeId)
            ->where('month', $month)
            ->update([
                'status' => 'pending',
            ]);

        return redirect()
            ->route('user.onboarding.checklist.index')
            ->with('success', 'Checklist saved & month status reset to pending');
    }
}
