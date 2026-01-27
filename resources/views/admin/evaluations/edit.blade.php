<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Edit Evaluation</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        <form method="POST" action="{{ route('admin.evaluations.update', $employee_evaluation->id) }}">

            @csrf
            @method('PUT')

        <div class="mb-4">
            <label>Employee</label>

            <!-- Select terlihat, tapi tidak bisa diedit -->
            <select class="border p-2 w-full rounded" disabled>
                @foreach($employees as $emp)
                    <option value="{{ $emp->employee_id }}"
                        {{ $emp->employee_id == $employee_evaluation->employee_id ? 'selected' : '' }}>
                        {{ $emp->employee_id }} - {{ $emp->name }}
                    </option>
                @endforeach
            </select>

            <!-- Hidden input supaya tetap terkirim ke server -->
            <input type="hidden" name="employee_id" value="{{ $employee_evaluation->id }}">
        </div>

        {{-- KPI TERAKHIR --}}
        @php
            $latestKpiPeriod = $employee_evaluation->kpi_december !== null
                ? 'December'
                : 'June';

            $latestKpiValue = $employee_evaluation->kpi_december
                ?? $employee_evaluation->kpi_june;
        @endphp

        <div class="mb-6 bg-gray-50 border rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-3">
                Latest KPI ({{ $latestKpiPeriod }})
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">

                <div>
                    <div class="text-gray-500">Business Aspect</div>
                    <div class="font-semibold">
                        {{ $employee_evaluation->business_score ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Behavior Aspect</div>
                    <div class="font-semibold">
                        {{ $employee_evaluation->behavior_score ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">PA Score</div>
                    <div class="font-semibold">
                        {{ $employee_evaluation->pa_score ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Total KPI</div>
                    <div class="font-bold text-indigo-600 text-lg">
                        {{ $latestKpiValue ?? '-' }}
                    </div>
                </div>

            </div>
        </div>



            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>KPI June</label>
                    <input type="number" step="0.01" name="kpi_june"
                           value="{{ $employee_evaluation->kpi_june }}"
                           class="border p-2 w-full rounded">
                </div>

                <div>
                    <label>KPI December</label>
                    <input type="number" step="0.01" name="kpi_december"
                           value="{{ $employee_evaluation->kpi_december }}"
                           class="border p-2 w-full rounded">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label>KPI June (Prev)</label>
                    <input type="number" step="0.01" name="kpi_june_prev"
                           value="{{ $employee_evaluation->kpi_june_prev }}"
                           class="border p-2 w-full rounded">
                </div>

                <div>
                    <label>KPI December (Prev)</label>
                    <input type="number" step="0.01" name="kpi_dec_prev"
                           value="{{ $employee_evaluation->kpi_dec_prev }}"
                           class="border p-2 w-full rounded">
                </div>
            </div>

            <div class="mt-4">
                <label>Assessment Link</label>
                <input type="text" name="assessment_link"
                       value="{{ $employee_evaluation->assessment_link }}"
                       class="border p-2 w-full rounded">
            </div>

            <button class="mt-5 bg-blue-600 text-white px-4 py-2 rounded">
                Update
            </button>
        </form>

    </div>
</x-app-layout>
