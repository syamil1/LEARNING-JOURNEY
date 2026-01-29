<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Introduction;
use App\Models\OnboardingChecklist;
use App\Models\Mentoring;
use App\Models\EmployeeTrainingScore;
use App\Models\EmployeeEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeReportController extends Controller
{
    /**
     * VIEW REPORT (WEB)
     */
    public function show(Employee $employee)
    {
        return view('admin.employee-report.show', [
            'employee'     => $employee->load(['store', 'job']),
            'introduction' => Introduction::where('nik', $employee->employee_id)->first(),
            'checklists'   => OnboardingChecklist::where('employee_id', $employee->employee_id)
                                ->orderBy('month')
                                ->orderBy('week')
                                ->get()
                                ->groupBy('month'),
            'mentoring'    => Mentoring::where('employee_id', $employee->employee_id)->get(),
            'development'  => EmployeeTrainingScore::where('employee_id', $employee->employee_id)->first(),
            'evaluation'   => EmployeeEvaluation::where('employee_id', $employee->employee_id)->first(),
        ]);
    }

    /**
     * DOWNLOAD PDF
     */
    public function pdf(Employee $employee)
    {
        $data = [
            'employee'     => $employee->load(['store', 'job']),
            'introduction' => Introduction::where('nik', $employee->employee_id)->first(),
            'checklists'   => OnboardingChecklist::where('employee_id', $employee->employee_id)
                                ->orderBy('month')
                                ->orderBy('week')
                                ->get()
                                ->groupBy('month'),
            'mentoring'    => Mentoring::where('employee_id', $employee->employee_id)->get(),
            'development'  => EmployeeTrainingScore::where('employee_id', $employee->employee_id)->first(),
            'evaluation'   => EmployeeEvaluation::where('employee_id', $employee->employee_id)->first(),
        ];

        $pdf = Pdf::loadView('admin.employee-report.pdf', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'Learning-Journey-' . $employee->employee_id . '.pdf'
        );
    }
}
