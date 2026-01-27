<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Store Manager Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 space-y-6">

            {{-- INFO TOKO --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Store</h3>
                    <p class="text-xl font-semibold">
                        {{ auth()->user()->store->name ?? '-' }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Email</h3>
                    <p class="text-lg">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-gray-500 text-sm">Role</h3>
                    <p class="text-lg capitalize">
                        {{ auth()->user()->role }}
                    </p>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-blue-50 p-6 rounded shadow">
                    <p class="text-sm text-gray-600">Total Supervisor</p>
                    <p class="text-3xl font-bold text-blue-700">
                        {{ $totalSpv }}
                    </p>
                </div>

                <div class="bg-yellow-50 p-6 rounded shadow">
                    <p class="text-sm text-gray-600">Onboarding Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        {{ $onboardingPending }}
                    </p>
                </div>

                <div class="bg-green-50 p-6 rounded shadow">
                    <p class="text-sm text-gray-600">Mentoring Active</p>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $mentoringActive }}
                    </p>
                </div>

                <div class="bg-red-50 p-6 rounded shadow">
                    <p class="text-sm text-gray-600">Development Pending</p>
                    <p class="text-3xl font-bold text-red-600">
                        {{ $developmentPending }}
                    </p>
                </div>
            </div>

{{-- LIST SUPERVISOR --}}
<div class="bg-white p-6 rounded shadow">
    <h3 class="text-lg font-semibold mb-4">Supervisor Evaluation</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Employee ID</th>
                    <th>Name</th>
                    <th>Introduction</th>
                    <th>6-Month Checklist</th>
                    <th>KPI June</th>
                    <th>KPI December</th>
                    <th>Total Mentoring</th>
                    <th>Avg Development</th>
                    <th>Assessment</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($supervisors as $ev)
                    @php $emp = $ev->employee; @endphp
                    <tr class="border-b">

                        {{-- EMPLOYEE ID --}}
                        <td class="py-2">{{ $emp->employee_id }}</td>

                        {{-- NAME --}}
                        <td>{{ $emp->name }}</td>


                        {{-- INTRO --}}
                        <td>
                            <span class="{{ $ev->intro_status === 'Sudah' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $ev->intro_status }}
                            </span>
                        </td>

                        {{-- CHECKLIST --}}
                        <td>
                            <span class="{{ $ev->checklist_summary === '6/6'
                                ? 'text-green-600 font-semibold'
                                : 'text-yellow-600 font-semibold' }}">
                                {{ $ev->checklist_summary }}
                            </span>
                        </td>

                        {{-- KPI --}}
                        <td>{{ $ev->kpi_june ?? '-' }}</td>
                        <td>{{ $ev->kpi_december ?? '-' }}</td>

                        {{-- MENTORING --}}
                        <td>
                            {{ $ev->total_mentoring > 0 ? $ev->total_mentoring.'x' : '0' }}
                        </td>

                        {{-- DEVELOPMENT --}}
                        <td>
                            @if ($ev->avg_development !== null)
                                <span class="font-semibold">{{ $ev->avg_development }}</span>
                            @else
                                <span class="text-red-500">Pending</span>
                            @endif
                        </td>

                        {{-- ASSESSMENT --}}
                        <td>
                            @if ($ev->assessment_link)
                                <a href="{{ $ev->assessment_link }}"
                                   target="_blank"
                                   class="text-blue-600 hover:underline">
                                    View
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td>
                            <a href="{{ route('admin.evaluations.edit', $ev->id) }}"
                               class="text-indigo-600 hover:underline">
                                Edit
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-gray-500">
                            No evaluation data found for this store.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
</x-app-layout>
