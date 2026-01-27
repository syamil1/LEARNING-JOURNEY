<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Introduction;
use App\Models\Region;
use App\Models\Store;
use App\Models\Section;
use App\Models\Job;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterRegion = $request->input('region');

        $employees = Employee::with(['region','store','section','job'])
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('employee_id', 'like', "%$search%")
                      ->orWhere('name', 'like', "%$search%");
                });
            })
            ->when($filterRegion, fn($query) => $query->where('region_id', $filterRegion))
            ->paginate(20);

        $regions = Region::all();

        return view('admin.employees.index', compact('employees', 'regions', 'search', 'filterRegion'));
    }

    public function show($id)
    {
        $employee = Employee::with(['region','store','section','job'])->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }
    public function create()
    {
        return view('admin.employees.create', [
            'regions' => \App\Models\Region::all(),
            'stores' => \App\Models\Store::all(),
            'sections' => \App\Models\Section::all(),
            'jobs' => \App\Models\Job::all(),
        ]);
    }

    public function search(Request $request)
    {
        $q = trim($request->q);

        if ($q === '') {
            return response()->json([]);
        }

        return Employee::where('employee_id', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->limit(10)
            ->get([
                'employee_id',
                'name'
            ]);
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees',
            'name' => 'required',
            'contract_type' => 'required|in:Permanent,Contract',
            'region_id' => 'required|exists:regions,id',
            'store_id' => 'required|exists:stores,id',
            'section_id' => 'required|exists:sections,id',
            'job_id' => 'required|exists:jobs,id',
            'birthday' => 'nullable|date',
            'initial_employment_date' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'permanent_date' => 'nullable|date',
        ]);
                
        Employee::create($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', [
            'employee' => $employee,
            'regions' => \App\Models\Region::all(),
            'stores' => \App\Models\Store::all(),
            'sections' => \App\Models\Section::all(),
            'jobs' => \App\Models\Job::all(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $employee->id,
            'name' => 'required',
            'contract_type' => 'required|in:Permanent,Contract',
            'region_id' => 'required|exists:regions,id',
            'store_id' => 'required|exists:stores,id',
            'section_id' => 'required|exists:sections,id',
            'job_id' => 'required|exists:jobs,id',
            'birthday' => 'nullable|date',
            'initial_employment_date' => 'nullable|date',
            'joining_date' => 'nullable|date', 
            'permanent_date' => 'nullable|date',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {

    
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted successfully');
        
    }

private function parseDate($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }

    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
        return \Carbon\Carbon::createFromFormat('d/m/Y', $value)
            ->format('Y-m-d');
    }

    return null;
}


public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $file = fopen($request->file('file'), 'r');

    if (!$file) {
        return back()->with('error', 'File cannot be opened.');
    }

    try {
        // Detect delimiter
        $firstLine = fgets($file);
        $delimiter = str_contains($firstLine, "\t") ? "\t"
                    : (str_contains($firstLine, ';') ? ';' : ',');
        rewind($file);

        // Skip header
        fgetcsv($file, 0, $delimiter);

        $read    = 0;
        $created = 0;
        $skipped = 0;

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {

            if (count($row) < 9 || trim($row[0]) === '') {
                $skipped++;
                continue;
            }

            $read++;

            $employeeId = trim($row[0]);

            // Skip if employee already exists
            if (DB::table('employees')->where('employee_id', $employeeId)->exists()) {
                $skipped++;
                continue;
            }

            // ✅ FIXED INDEX
            $regionId = $row[3] ?? null;
            $storeId  = $row[4] ?? null;

            // Validate relation
            if (
                !DB::table('regions')->where('id', $regionId)->exists() ||
                !DB::table('stores')->where('id', $storeId)->exists()
            ) {
                $skipped++;
                continue;
            }

            DB::table('employees')->insert([
                'employee_id'             => $employeeId,
                'name'                    => trim($row[1]),
                'contract_type'           => trim($row[2]),

                'region_id'               => $regionId,
                'store_id'                => $storeId,

                // DEFAULT VALUE
                'section_id'              => 1,
                'job_id'                  => 1,

                'birthday'                => $this->parseDate($row[5] ?? null),
                'initial_employment_date' => $this->parseDate($row[6] ?? null),
                'joining_date'            => $this->parseDate($row[7] ?? null),
                'permanent_date'          => $this->parseDate($row[8] ?? null),

                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            $created++;
        }

        fclose($file);

        return redirect()
            ->route('admin.employees.index')
            ->with(
                'success',
                "Import Employees completed — Read: {$read}, Created: {$created}, Skipped: {$skipped}."
            );

    } catch (\Throwable $e) {
        fclose($file);
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}




}
