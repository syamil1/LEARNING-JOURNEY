<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeEvaluation;
use App\Models\Employee;
use App\Models\Introduction;
use Illuminate\Http\Request;

class EmployeeEvaluationController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->search;
        $sort   = $request->sort;

        $query = EmployeeEvaluation::query()
            ->select('employee_evaluations.*')
            ->with([
                'employee.store',
                'employee.introduction',
                'employee.onboardingChecklists',
                'employee.mentorings',
                'employee.developmentScore',
            ])

            ->when($search, function ($q) use ($search) {
                $q->whereHas('employee', function ($emp) use ($search) {
                    $emp->where('employee_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            });

        /**
         * SORTING
         */
        if ($sort) {
            switch ($sort) {

                // KPI JUNE
                case 'kpi_june_desc':
                    $query->orderByDesc('kpi_june');
                    break;

                case 'kpi_june_asc':
                    $query->orderBy('kpi_june');
                    break;

                // KPI DECEMBER
                case 'kpi_december_desc':
                    $query->orderByDesc('kpi_december');
                    break;

                case 'kpi_december_asc':
                    $query->orderBy('kpi_december');
                    break;

                // AVG DEVELOPMENT (HITUNG LANGSUNG DI SQL)
                case 'development_desc':
                    $query
                        ->leftJoin(
                            'employee_training_scores',
                            'employee_training_scores.employee_id',
                            '=',
                            'employee_evaluations.employee_id'
                        )
                        ->selectRaw('(
                            (
                                employee_training_scores.rso_supervisory_skill +
                                employee_training_scores.rso_retail_salesmanship +
                                employee_training_scores.rso_customer_service_loyalty +
                                employee_training_scores.rso_product_merchandising +
                                employee_training_scores.rso_visual_merchandising +
                                employee_training_scores.rso_retail_store_promotion +
                                employee_training_scores.rso_store_financial_perspective +
                                employee_training_scores.rso_store_general_checkup_strategy
                            ) / 8
                        ) as rso_average_calc')
                        ->orderByDesc('rso_average_calc');
                    break;

                case 'development_asc':
                    $query
                        ->leftJoin(
                            'employee_training_scores',
                            'employee_training_scores.employee_id',
                            '=',
                            'employee_evaluations.employee_id'
                        )
                        ->selectRaw('(
                            (
                                employee_training_scores.rso_supervisory_skill +
                                employee_training_scores.rso_retail_salesmanship +
                                employee_training_scores.rso_customer_service_loyalty +
                                employee_training_scores.rso_product_merchandising +
                                employee_training_scores.rso_visual_merchandising +
                                employee_training_scores.rso_retail_store_promotion +
                                employee_training_scores.rso_store_financial_perspective +
                                employee_training_scores.rso_store_general_checkup_strategy
                            ) / 8
                        ) as rso_average_calc')
                        ->orderBy('rso_average_calc');
                    break;
            }
        }

        $evaluations = $query
            ->paginate(10)
            ->withQueryString();
 
        /**
         * HITUNG DATA TAMBAHAN (LOGIC LAMA KAMU)
         */
        foreach ($evaluations as $ev) {

            $employee = $ev->employee;
            $intro = $employee->introduction;

            $ev->intro_status = ($intro && (
                $intro->fgd_analytic_score ||
                $intro->fgd_business_score ||
                $intro->fgd_leadership_score ||
                $intro->interview_analytic_score ||
                $intro->interview_business_score ||
                $intro->interview_leadership_score
            )) ? 'Sudah' : 'Belum';

            $checklists = $employee->onboardingChecklists;

            if ($checklists->contains(fn ($c) => $c->month == 0)) {
                $ev->checklist_summary = '6/6';
            } else {
                $approved = 0;
                $grouped = $checklists
                    ->whereBetween('month', [1, 6])
                    ->groupBy('month');

                foreach ($grouped as $weeks) {
                    if (
                        $weeks->count() === 4 &&
                        $weeks->every(fn ($w) => $w->status === 'approved')
                    ) {
                        $approved++;
                    }
                }

                $ev->checklist_summary = $approved . '/6';
            }

            $ev->total_mentoring = $employee->mentorings
                ->where('status', 'verified')
                ->count();

            // DISPLAY (ACCESSOR AMAN DI SINI)
            $ev->avg_development = $employee->developmentScore
                ? $employee->developmentScore->rso_average
                : null;
        }

        return view('admin.evaluations.index', compact('evaluations', 'search', 'sort'));
    }




    public function create()
    {
        $employees = Employee::select('employee_id', 'name')->get();

        return view('admin.evaluations.create', compact('employees'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,employee_id|unique:employee_evaluations,employee_id',
            'kpi_june' => 'required|numeric|min:0',
            'kpi_december' => 'required|numeric|min:0',
            'kpi_last_year_june' => 'nullable|numeric|min:0',
            'kpi_last_year_december' => 'nullable|numeric|min:0',
            'assessment_link' => 'nullable|string',
        ]);

        EmployeeEvaluation::create($validated);

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Employee evaluation saved.');
    }

    public function edit(EmployeeEvaluation $evaluation)
    {
        $employees = \App\Models\Employee::all();

        return view('admin.evaluations.edit', [
            'employee_evaluation' => $evaluation,
            'employees' => $employees
        ]);
    }

    public function update(Request $request, EmployeeEvaluation $evaluation)
    {
        $validated = $request->validate([
            'kpi_june' => 'required|numeric|min:0',
            'kpi_december' => 'required|numeric|min:0',
            'kpi_last_year_june' => 'nullable|numeric|min:0',
            'kpi_last_year_december' => 'nullable|numeric|min:0',
            'assessment_link' => 'nullable|string',
        ]);

        $evaluation->update($validated);

        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Employee evaluation updated.');
    }

    public function destroy(EmployeeEvaluation $evaluation)
    {
        $evaluation->delete();
        return redirect()->route('admin.evaluations.index')
            ->with('success', 'Employee evaluation deleted.');
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q'));

        if ($q === '') {
            return response()->json([]);
        }

        $employees = Employee::where('employee_id', 'LIKE', "%{$q}%")
            ->orWhere('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get(['employee_id', 'name']);

        return response()->json($employees);
    }

public function export(Request $request)
{
    $search = $request->search;


    $evaluations = EmployeeEvaluation::with([
        'employee.store',
        'employee.introduction',
        'employee.onboardingChecklists',
        'employee.mentorings',
        'employee.developmentScore',
    ])
    ->when($search, function ($query) use ($search) {
        $query->whereHas('employee', function ($q) use ($search) {
            $q->where('employee_id', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%");
        });
    })
    ->orderByDesc('created_at')
    ->get()
    ->groupBy('employee_id')
    ->map(fn ($rows) => $rows->first());

    foreach ($evaluations as $ev) {

        $employee = $ev->employee;

        $intro = $employee->introduction;
        $ev->intro_status = ($intro && (
            $intro->fgd_analytic_score ||
            $intro->fgd_business_score ||
            $intro->fgd_leadership_score ||
            $intro->interview_analytic_score ||
            $intro->interview_business_score ||
            $intro->interview_leadership_score
        )) ? 'Sudah' : 'Belum';

        $checklists = $employee->onboardingChecklists;

        if ($checklists->contains(fn ($c) => $c->month == 0)) {
            $ev->checklist_summary = '6/6';
        } else {
            $approved = 0;
            $grouped = $checklists
                ->whereBetween('month', [1, 6])
                ->groupBy('month');

            foreach ($grouped as $weeks) {
                if (
                    $weeks->count() === 4 &&
                    $weeks->every(fn ($w) => $w->status === 'approved')
                ) {
                    $approved++;
                }
            }

            $ev->checklist_summary = $approved . '/6';
        }

        $ev->total_mentoring = $employee->mentorings
            ->where('status', 'verified')
            ->count();

        $ev->avg_development = $employee->developmentScore
            ? $employee->developmentScore->rso_average
            : 'Pending';
    }

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename=employee-evaluation.csv',
    ];

    return response()->stream(function () use ($evaluations) {

        $file = fopen('php://output', 'w');

        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, [
            'Employee ID',
            'Employee Name',
            'Store',
            'Introduction',
            '6-Month Checklist',
            'KPI June',
            'KPI December',
            'Total Mentoring',
            'Avg Development',
            'Assessment Link',
        ]);

        foreach ($evaluations as $ev) {

            $emp = $ev->employee;
            if (!$emp) continue;

        fputcsv($file, [
            "'".$emp->employee_id,           
            $this->cleanCsv($emp->name),
            $this->cleanCsv($emp->store->name ?? '-'),
            $ev->intro_status,

            "'".$ev->checklist_summary,        

            $ev->kpi_june !== null ? $ev->kpi_june : '-',
            $ev->kpi_december !== null ? $ev->kpi_december : '-',

            $ev->total_mentoring . 'x',

            is_numeric($ev->avg_development)
                ? $ev->avg_development
                : 'Pending',

            $this->cleanCsv($ev->assessment_link ?? '-'),
        ]);

        }

        fclose($file);

    }, 200, $headers);
}


private function cleanCsv($value)
{
    if ($value === null) return '-';

    return trim(
        preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $value))
    );
}

public function import(Request $request, string $period)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    if (!in_array($period, ['june', 'december'])) {
        abort(404);
    }

    $file = fopen($request->file('file'), 'r');

    $firstLine = fgets($file);
    if (str_contains($firstLine, ';')) {
        $delimiter = ';';
    } elseif (str_contains($firstLine, "\t")) {
        $delimiter = "\t";
    } else {
        $delimiter = ',';
    }
    rewind($file);


    fgetcsv($file, 0, $delimiter);

    $read    = 0;
    $created = 0;
    $updated = 0;
    $skipped = 0;

    while (($row = fgetcsv($file, 0, $delimiter)) !== false) {

     dd($row);
        if (count($row) < 3 || trim($row[0]) === '') {
            $skipped++;
            continue;
        }

        $read++;

        $employeeId = trim($row[0]);
        $business   = (float) str_replace(',', '.', $row[1] ?? 0);
        $behavior   = (float) str_replace(',', '.', $row[2] ?? 0);
        $pa         = (float) str_replace(',', '.', $row[3] ?? 0);

        $totalKpi = $business + $behavior + $pa;

        if (!\App\Models\Employee::where('employee_id', $employeeId)->exists()) {
            $skipped++;
            continue;
        }

        $data = [
            'business_score' => $business,
            'behavior_score' => $behavior,
            'pa_score'       => $pa,
        ];

        if ($period === 'june') {
            $data['kpi_june'] = $totalKpi;
        } else {
            $data['kpi_december'] = $totalKpi;
        }

        $evaluation = \App\Models\EmployeeEvaluation::where(
            'employee_id',
            $employeeId
        )->first();

        if ($evaluation) {
            $evaluation->update($data);
            $updated++;
        } else {
            \App\Models\EmployeeEvaluation::create(
                array_merge(['employee_id' => $employeeId], $data)
            );
            $created++;
        }
    }

    fclose($file);

    return back()->with(
        'success',
        "Import KPI completed. 
        Read: {$read}, 
        Created: {$created}, 
        Updated: {$updated}, 
        Skipped: {$skipped}."
    );

}

public function importAssessment(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $file = fopen($request->file('file'), 'r');

    $firstLine = fgets($file);

    if (str_contains($firstLine, ';')) {
        $delimiter = ';';
    } elseif (str_contains($firstLine, "\t")) {
        $delimiter = "\t";
    } else {
        $delimiter = ',';
    }
    rewind($file);


    fgetcsv($file, 0, $delimiter);

    $read    = 0;
    $created = 0;
    $updated = 0;
    $skipped = 0;

    while (($row = fgetcsv($file, 0, $delimiter)) !== false) {

        if (count($row) < 2 || trim($row[0]) === '') {
            $skipped++;
            continue;
        }

        $read++;

        $employeeId     = trim($row[0]);
        $assessmentLink = trim($row[1]);

        if (!Employee::where('employee_id', $employeeId)->exists()) {
            $skipped++;
            continue;
        }

        $evaluation = EmployeeEvaluation::where(
            'employee_id',
            $employeeId
        )->first();

        if ($evaluation) {
            if ($evaluation->assessment_link !== $assessmentLink) {
                $evaluation->update([
                    'assessment_link' => $assessmentLink
                ]);
                $updated++;
            }
        } else {
            EmployeeEvaluation::create([
                'employee_id'     => $employeeId,
                'assessment_link' => $assessmentLink
            ]);
            $created++;
        }
    }

    fclose($file);

    return back()->with(
        'success',
        "Import Assessment completed. 
        Read: {$read}, 
        Created: {$created}, 
        Updated: {$updated}, 
        Skipped: {$skipped}."
    );

}


}
