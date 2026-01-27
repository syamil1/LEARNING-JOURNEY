<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Introduction;
use App\Models\Employee;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IntroductionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $pic = $request->input('pic');

        $introductions = Introduction::query()
            ->leftJoin('employees', 'introductions.nik', '=', 'employees.employee_id')
            ->select('introductions.*', 'employees.name as employee_name','employees.joining_date as joining_date')
            ->when($search, function ($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('introductions.nik', 'like', "%$search%")
                        ->orWhere('employees.name', 'like', "%$search%");
                });
            })
            ->when($pic, fn($q) => $q->where('introductions.pic', $pic))
            ->orderBy('introductions.id', 'desc')
            ->paginate(20);

        $pics = Introduction::select('pic')->distinct()->pluck('pic');

        return view('admin.introductions.index', compact('introductions', 'search', 'pic', 'pics'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $results = Employee::query()
            ->where('employee_id', 'LIKE', "%{$q}%")
            ->orWhere('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get(['employee_id', 'name']);

        return response()->json($results);
    }



    public function show($id)
    {
        $introduction = Introduction::query()
            ->leftJoin('employees', 'introductions.nik', '=', 'employees.employee_id')
            ->select(
                'introductions.*',
                'employees.name as employee_name'
            )
            ->where('introductions.id', $id)
            ->firstOrFail();

        return view('admin.introductions.show', compact('introduction'));
    }


    public function create()
    {
        $employees = Employee::select('employee_id', 'name')->get();
        $introduction = new Introduction(); 

        return view('admin.introductions.create', compact('employees', 'introduction'));
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:introductions,nik',

            'fgd_analytic_score' => 'nullable|integer|in:1,2,3',
            'fgd_business_score' => 'nullable|integer|in:1,2,3',
            'fgd_leadership_score' => 'nullable|integer|in:1,2,3',

            'interview_analytic_score' => 'nullable|integer|in:1,2,3',
            'interview_business_score' => 'nullable|integer|in:1,2,3',
            'interview_leadership_score' => 'nullable|integer|in:1,2,3',

            'fgd_note' => 'nullable|string',
            'interview_note' => 'nullable|string',
            'mcu' => 'nullable|string',
            'psikotes' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'pic' => 'nullable|string',
        ]);

        Introduction::create($validated);

        return redirect()
            ->route('admin.introductions.index')
            ->with('success', 'Introduction created successfully.');
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }


    public function upload()
    {
        return view('admin.introductions.upload');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        if (!$file) {
            return back()->with('error', 'File tidak bisa dibuka.');
        }

        try {
            // ===============================
            // DETEKSI DELIMITER
            // ===============================
            $firstLine = fgets($file);

            if (str_contains($firstLine, "\t")) {
                $delimiter = "\t";
            } elseif (str_contains($firstLine, ';')) {
                $delimiter = ';';
            } else {
                $delimiter = ',';
            }

            rewind($file);

            // Skip header
            fgetcsv($file, 0, $delimiter);

            // ===============================
            // COUNTER
            // ===============================
            $read    = 0;
            $created = 0;
            $updated = 0; // disiapkan kalau nanti mau update
            $skipped = 0;

            while (($row = fgetcsv($file, 0, $delimiter)) !== false) {

                // validasi dasar
                if (count($row) < 10 || trim($row[0]) === '') {
                    $skipped++;
                    continue;
                }

                $read++;

                $employeeId = trim($row[0]);

                // skip jika employee sudah ada
                if (DB::table('employees')->where('employee_id', $employeeId)->exists()) {
                    $skipped++;
                    continue;
                }

                $regionId  = $row[3];
                $storeId   = $row[4];
                $sectionId = $row[5];
                $jobId     = 1;

                // validasi foreign key
                if (
                    !DB::table('regions')->where('id', $regionId)->exists() ||
                    !DB::table('stores')->where('id', $storeId)->exists() ||
                    !DB::table('sections')->where('id', $sectionId)->exists() ||
                    !DB::table('jobs')->where('id', $jobId)->exists()
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
                    'section_id'              => $sectionId,
                    'job_id'                  => $jobId,
                    'birthday'                => $this->parseDate($row[6] ?? null),
                    'initial_employment_date' => $this->parseDate($row[7] ?? null),
                    'joining_date'            => $this->parseDate($row[8] ?? null),
                    'permanent_date'          => $this->parseDate($row[9] ?? null),
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);

                $created++;
            }

            fclose($file);

            return redirect()
                ->route('admin.introductions.index')
                ->with(
                    'success',
                    "Import Employees selesai.
                    {$read} dibaca,
                    {$created} dibuat,
                    {$updated} diperbarui,
                    {$skipped} dilewati."
                );

        } catch (\Throwable $e) {
            fclose($file);

            return back()->with('error', 
                "Import gagal: {$e->getMessage()} ({$e->getFile()}:{$e->getLine()})"
            );
        }
    }

    public function edit($id)
    {
        $introduction = Introduction::findOrFail($id);
        $levels = Level::all();
        $employees = Employee::all();



        return view('admin.introductions.edit', compact('introduction', 'employees', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string',
            'fgd_analytic_score' => 'nullable|integer',
            'fgd_analytic_level_id' => 'nullable|exists:levels,id',

            'fgd_business_score' => 'nullable|integer',
            'fgd_business_level_id' => 'nullable|exists:levels,id',

            'fgd_leadership_score' => 'nullable|integer',
            'fgd_leadership_level_id' => 'nullable|exists:levels,id',

            'interview_analytic_score' => 'nullable|integer',
            'interview_analytic_level_id' => 'nullable|exists:levels,id',

            'interview_business_score' => 'nullable|integer',
            'interview_business_level_id' => 'nullable|exists:levels,id',

            'interview_leadership_score' => 'nullable|integer',
            'interview_leadership_level_id' => 'nullable|exists:levels,id',

            'fgd_note' => 'nullable|string',
            'interview_note' => 'nullable|string',
            'mcu' => 'nullable|string',
            'psikotes' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'pic' => 'nullable|string',
        ]);

        $introduction = Introduction::findOrFail($id);
        $introduction->update($validated);

        return redirect()
            ->route('admin.introductions.index')
            ->with('success', 'Introduction updated successfully.');
    }


    public function destroy(Introduction $introduction)
    {
        $introduction->delete();
        return redirect()->route('admin.introductions.index')->with('success', 'Deleted successfully.');
    }
}
