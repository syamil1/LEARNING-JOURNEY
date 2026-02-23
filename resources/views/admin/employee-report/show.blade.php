<x-app-layout>
         @php
        function scoreLevel($score) {
            if ($score === null) return '-';

            return match ((int)$score) {
                1 => 'Lone Ranger',
                2 => 'Team Player',
                3 => 'Team Leader',
                default => '-'
            };
        }

        function scoreBadgeClass($score) {
            return match ((int)$score) {
                1 => 'bg-red-100 text-red-700',
                2 => 'bg-yellow-100 text-yellow-700',
                3 => 'bg-green-100 text-green-700',
                default => 'bg-gray-100 text-gray-500'
            };
        }
        @endphp
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">
                Learning Journey Report
            </h2>

            <a href="{{ route('admin.employees.report.pdf', $employee) }}"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Download PDF
            </a>
        </div>
    </x-slot>

<div class="py-6 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ================= LEFT COLUMN ================= --}}
    <div class="space-y-6">

    
        {{-- ================= USER ACCOUNT ================= --}}
        <div class="bg-white shadow rounded-xl p-6 border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 text-center">User Account</h3>

            <div class="max-w-md mx-auto">
                <div class="grid grid-cols-2 gap-8 text-sm text-center">

                    {{-- NAME --}}
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Name</p>
                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $userAccount->name ?? '-' }}
                        </p>
                    </div>

                    {{-- LOGIN ID --}}
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide">Login ID</p>
                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $userAccount->email ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- ACTION --}}
            @if(auth()->user()->role === 'admin')
            <div class="mt-6 flex flex-col items-center gap-3 border-t pt-4">

                <form method="POST" action="{{ route('admin.users.reset-password', $employee->employee_id) }}">
                    @csrf
                    @method('PUT')

                    <button type="submit"
                        onclick="return confirm('Reset password ke default (12345678)?')"
                        class="bg-yellow-500 text-white px-5 py-2 rounded-lg hover:bg-yellow-600 transition">
                        🔄 Reset Password
                    </button>
                </form>

            </div>
            @endif
        </div>


        {{-- ================= EMPLOYEE INFO ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Employee Information</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-gray-500">Employee ID</p>
            <p class="font-semibold">{{ $employee->employee_id }}</p>
        </div>

        <div>
            <p class="text-gray-500">Name</p>
            <p class="font-semibold">{{ $employee->name }}</p>
        </div>

        <div>
            <p class="text-gray-500">Contract Type</p>
            <p class="font-semibold">{{ $employee->contract_type ?? '-' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Division</p>
            <p class="font-semibold">{{ $employee->region->name ?? '-' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Store</p>
            <p class="font-semibold">{{ $employee->store->name ?? '-' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Section</p>
            <p class="font-semibold">{{ $employee->section->name ?? '-' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Job</p>
            <p class="font-semibold">{{ $employee->job->name ?? '-' }}</p>
        </div>

        <div>
            <p class="text-gray-500">Birthday</p>
            <p class="font-semibold">
                {{ $employee->birthday ? \Carbon\Carbon::parse($employee->birthday)->format('d M Y') : '-' }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Joining Date</p>
            <p class="font-semibold">
                {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : '-' }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Initial Employment</p>
            <p class="font-semibold">
                {{ $employee->initial_employment_date
                    ? \Carbon\Carbon::parse($employee->initial_employment_date)->format('d M Y')
                    : '-' }}
            </p>
        </div>

        <div>
            <p class="text-gray-500">Permanent Date</p>
            <p class="font-semibold">
                {{ $employee->permanent_date
                    ? \Carbon\Carbon::parse($employee->permanent_date)->format('d M Y')
                    : '-' }}
            </p>
        </div>
            </div>
        </div>


        {{-- ================= INTRODUCTION ================= --}}
        <div class="bg-white shadow rounded p-6 space-y-6">
            <h3 class="text-lg font-semibold">Introduction Assessment</h3>

            @if($introduction)
            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100 text-center">
                        <tr>
                            <th class="p-2 border text-left"></th>
                            <th class="p-2 border">Analytic</th>
                            <th class="p-2 border">Business</th>
                            <th class="p-2 border">Leadership</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border font-semibold">FGD</td>
                            @foreach([
                                $introduction->fgd_analytic_score,
                                $introduction->fgd_business_score,
                                $introduction->fgd_leadership_score
                            ] as $score)
                            <td class="p-2 border text-center">
                                {{ $score ?? '-' }}
                                @if($score !== null)
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded {{ scoreBadgeClass($score) }}">
                                        {{ scoreLevel($score) }}
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        <tr>
                            <td class="p-2 border font-semibold">Interview</td>
                            @foreach([
                                $introduction->interview_analytic_score,
                                $introduction->interview_business_score,
                                $introduction->interview_leadership_score
                            ] as $score)
                            <td class="p-2 border text-center">
                                {{ $score ?? '-' }}
                                @if($score !== null)
                                    <span class="ml-2 px-2 py-0.5 text-xs rounded {{ scoreBadgeClass($score) }}">
                                        {{ scoreLevel($score) }}
                                    </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Recommendation</p>
                    <p class="font-medium">{{ $introduction->rekomendasi ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">FGD Note</p>
                    <p>{{ $introduction->fgd_note ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Interview Note</p>
                    <p>{{ $introduction->interview_note ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm pt-4 border-t">
                <div>
                    <p class="text-gray-500">PIC</p>
                    <p class="font-semibold">{{ $introduction->pic ?? '-' }}</p>
                </div>
            </div>
            @else
                <p class="text-gray-400">No introduction data</p>
            @endif
        </div>

        {{-- ================= ONBOARDING ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Onboarding Checklist (6 Months)</h3>
            @if($isAutoGenerated)
                <div class="mb-3 inline-flex items-center gap-2 px-3 py-1 rounded bg-blue-100 text-blue-800 text-sm">
                    Auto generated onboarding checklist
                </div>
            @endif
            <canvas id="onboardingChart" height="120"></canvas>
        </div>

    </div>

    {{-- ================= RIGHT COLUMN ================= --}}
    <div class="space-y-6">

        {{-- ================= DEVELOPMENT ================= --}}
        @if($development)
        <div class="grid grid-cols-1 gap-6 bg-white shadow rounded p-6">
            <div class="bg-gray-50 border rounded-lg p-4 flex justify-center">
                <canvas id="developmentChart" class="max-h-[260px]"></canvas>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Competency</th>
                            <th class="p-2 border text-center">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-2 border">Supervisory Skill</td><td class="p-2 border text-center">{{ $development->rso_supervisory_skill }}</td></tr>
                        <tr><td class="p-2 border">Retail Salesmanship</td><td class="p-2 border text-center">{{ $development->rso_retail_salesmanship }}</td></tr>
                        <tr><td class="p-2 border">Customer Service Loyalty</td><td class="p-2 border text-center">{{ $development->rso_customer_service_loyalty }}</td></tr>
                        <tr><td class="p-2 border">Product Merchandising</td><td class="p-2 border text-center">{{ $development->rso_product_merchandising }}</td></tr>
                        <tr><td class="p-2 border">Visual Merchandising</td><td class="p-2 border text-center">{{ $development->rso_visual_merchandising }}</td></tr>
                        <tr><td class="p-2 border">Retail Store Promotion</td><td class="p-2 border text-center">{{ $development->rso_retail_store_promotion }}</td></tr>
                        <tr><td class="p-2 border">Financial Perspective</td><td class="p-2 border text-center">{{ $development->rso_store_financial_perspective }}</td></tr>
                        <tr><td class="p-2 border">General Strategy</td><td class="p-2 border text-center">{{ $development->rso_store_general_checkup_strategy }}</td></tr>
                    </tbody>
                </table>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-sm border">
                        <tbody>
                            <tr class="bg-gray-50">
                                <td class="p-2 border font-semibold w-1/3">Learning Hours</td>
                                <td class="p-2 border">{{ $development->learning_hours }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 border font-semibold">Nilai NGECAS</td>
                                <td class="p-2 border">{{ $development->nilai_ngecas }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-2 border font-semibold">Compulsory Training</td>
                                <td class="p-2 border">{{ $development->compulsory_training }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 border font-semibold">Optional Training</td>
                                <td class="p-2 border">{{ $development->optional_training }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-2 border font-semibold">Development Program</td>
                                <td class="p-2 border">
                                    {{ $development->development_program ?? '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    {{-- ================= MENTORING ================= --}}
    <div class="bg-white shadow rounded p-6">
        <h3 class="text-lg font-semibold mb-4">Mentoring Records</h3>

        @if($mentoring->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Date</th>
                            <th class="p-2 border">Mentor</th>
                            <th class="p-2 border">Store</th>
                            <th class="p-2 border">Notes</th>
                            <th class="p-2 border">HR Notes</th>
                            <th class="p-2 border text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mentoring as $m)
                            <tr>
                                <td class="p-2 border">
                                    {{ $m->created_at?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="p-2 border">
                                    {{ $m->mentor_name ?? '-' }}
                                </td>
                                <td class="p-2 border">
                                    {{ $m->store->name ?? '-' }}
                                </td>
                                <td class="p-2 border">
                                    {{ $m->notes ?? '-' }}
                                </td>
                                <td class="p-2 border">
                                    {{ $m->notes_hr ?? '-' }}
                                </td>
                                <td class="p-2 border text-center">
                                    @if($m->status === 'verified')
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                            Verified
                                        </span>
                                    @elseif($m->status === 'pending')
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                                            {{ ucfirst($m->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-gray-700">
                <strong>Total Mentoring:</strong>
                {{ $mentoring->count() }} |
                <strong>Verified:</strong>
                {{ $mentoring->where('status','verified')->count() }}
            </div>
        @else
            <p class="text-gray-400">No mentoring records available</p>
        @endif
    </div>


    {{-- ================= KPI ================= --}}
    <div class="bg-white shadow rounded p-6 space-y-4">
        <h3 class="text-lg font-semibold">Performance (KPI)</h3>

        {{-- KPI CHART --}}
        <canvas id="kpiChart" height="120"></canvas>

        @if($evaluation)
            {{-- KPI TABLE --}}
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Aspect</th>
                            <th class="p-2 border text-center">June</th>
                            <th class="p-2 border text-center">December</th>
                            <th class="p-2 border text-center">Last Year June</th>
                            <th class="p-2 border text-center">Last Year December</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border">Business</td>
                            <td class="p-2 border text-center">{{ $evaluation->business_score ?? '-' }}</td>
                            <td class="p-2 border text-center">{{ $evaluation->kpi_december ?? '-' }}</td>
                            <td class="p-2 border text-center">{{ $evaluation->last_year_kpi_june ?? '-' }}</td>
                            <td class="p-2 border text-center">{{ $evaluation->last_year_kpi_december ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="p-2 border">Behavior</td>
                            <td class="p-2 border text-center">{{ $evaluation->behavior_score ?? '-' }}</td>
                            <td class="p-2 border text-center">-</td>
                            <td class="p-2 border text-center">-</td>
                            <td class="p-2 border text-center">-</td>
                        </tr>
                        <tr>
                            <td class="p-2 border">PA</td>
                            <td class="p-2 border text-center">{{ $evaluation->pa_score ?? '-' }}</td>
                            <td class="p-2 border text-center">-</td>
                            <td class="p-2 border text-center">-</td>
                            <td class="p-2 border text-center">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ASSESSMENT LINK --}}
            @if($evaluation->assessment_link)
                <p class="text-sm mt-2">
                    <strong>Assessment Link:</strong>
                    <a href="{{ $evaluation->assessment_link }}"
                    target="_blank"
                    class="text-blue-600 underline">
                        View Assessment
                    </a>
                </p>
            @endif
        @else
            <p class="text-gray-400 text-sm">No KPI data available</p>
        @endif
</div>


    </div>

</div>


    {{-- ================= CHART JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      <script>
        // ================= ONBOARDING =================
        const onboardingData = @json($onboardingData);


        new Chart(document.getElementById('onboardingChart'), {
            type: 'bar',
            data: {
                labels: ['Month 1','Month 2','Month 3','Month 4','Month 5','Month 6'],
                datasets: [{
                    label: 'Completion (%)',
                    data: onboardingData,
                    backgroundColor: '#4f46e5'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });

        // ================= KPI =================
        new Chart(document.getElementById('kpiChart'), {
            type: 'line',
            data: {
                labels: ['June', 'December'],
                datasets: [{
                    label: 'KPI Score',
                    data: [
                        {{ $evaluation->kpi_june ?? 0 }},
                        {{ $evaluation->kpi_december ?? 0 }}
                    ],
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 400 }
                }
            }
        });

      
    @if($development)
    new Chart(document.getElementById('developmentChart'), {
        type: 'radar',
        data: {
            labels: [
                'Supervisory Skill',
                'Retail Salesmanship',
                'Customer Service Loyalty',
                'Product Merchandising',
                'Visual Merchandising',
                'Retail Store Promotion',
                'Financial Perspective',
                'General Strategy'
            ],
            datasets: [{
                label: 'Development Score',
                data: [
                    {{ $development->rso_supervisory_skill ?? 0 }},
                    {{ $development->rso_retail_salesmanship ?? 0 }},
                    {{ $development->rso_customer_service_loyalty ?? 0 }},
                    {{ $development->rso_product_merchandising ?? 0 }},
                    {{ $development->rso_visual_merchandising ?? 0 }},
                    {{ $development->rso_retail_store_promotion ?? 0 }},
                    {{ $development->rso_store_financial_perspective ?? 0 }},
                    {{ $development->rso_store_general_checkup_strategy ?? 0 }},
                ],
                backgroundColor: 'rgba(79,70,229,0.2)',
                borderColor: '#4f46e5',
                borderWidth: 2,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 20
                    }
                }
            }
        }
    });
    @endif
    </script>

</x-app-layout>
