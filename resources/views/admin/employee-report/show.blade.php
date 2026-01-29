<x-app-layout>
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

    <div class="py-6 max-w-7xl mx-auto space-y-6">

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
                    <p class="text-gray-500">Store</p>
                    <p class="font-semibold">{{ $employee->store->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Job</p>
                    <p class="font-semibold">{{ $employee->job->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- ================= INTRODUCTION ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Introduction Assessment</h3>

            @if($introduction)
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-gray-500">FGD Avg</p>
                        <p class="text-2xl font-bold">
                            {{
                                collect([
                                    $introduction->fgd_analytic_score,
                                    $introduction->fgd_business_score,
                                    $introduction->fgd_leadership_score
                                ])->filter()->avg()
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Interview Avg</p>
                        <p class="text-2xl font-bold">
                            {{
                                collect([
                                    $introduction->interview_analytic_score,
                                    $introduction->interview_business_score,
                                    $introduction->interview_leadership_score
                                ])->filter()->avg()
                            }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Recommendation</p>
                        <p class="font-semibold">
                            {{ $introduction->rekomendasi ?? '-' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-gray-400">No introduction data</p>
            @endif
        </div>

        {{-- ================= ONBOARDING CHECKLIST ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">
                Onboarding Checklist (6 Months)
            </h3>

            <canvas id="onboardingChart" height="120"></canvas>
        </div>

        {{-- ================= DEVELOPMENT ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Development & Training</h3>

            @if($development)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-gray-500">Learning Hours</p>
                        <p class="text-2xl font-bold">
                            {{ $development->learning_hours ?? 0 }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Avg RSO</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($development->rso_average, 1) }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Development Program</p>
                        <p class="font-semibold">
                            {{ $development->development_program ?? '-' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-gray-400">No development data</p>
            @endif
        </div>

        {{-- ================= KPI ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Performance (KPI)</h3>

            <canvas id="kpiChart" height="120"></canvas>
        </div>

    </div>

    {{-- ================= CHART JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ================= ONBOARDING =================
        const onboardingData = @json(
            collect(range(1,6))->map(fn($m) =>
                isset($checklists[$m]) &&
                $checklists[$m]->every(fn($w) => $w->status === 'approved')
                ? 100 : 0
            )
        );

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
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>

</x-app-layout>
