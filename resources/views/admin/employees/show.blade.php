<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="flex justify-center items-center py-6 min-h-screen bg-gray-100">
        <div class="w-full max-w-4xl bg-white p-6 shadow rounded">

            <table class="min-w-full text-left mx-auto text-black">
                <tr><th class="p-2">Employee ID</th><td class="p-2">{{ $employee->employee_id }}</td></tr>
                <tr><th class="p-2">Name</th><td class="p-2">{{ $employee->name }}</td></tr>
                <tr><th class="p-2">Contract Type</th><td class="p-2">{{ $employee->contract_type }}</td></tr>

                <tr><th class="p-2">Region</th><td class="p-2">{{ $employee->region->name ?? '-' }}</td></tr>
                <tr><th class="p-2">Store</th><td class="p-2">{{ $employee->store->name ?? '-' }}</td></tr>
                <tr><th class="p-2">Section</th><td class="p-2">{{ $employee->section->name ?? '-' }}</td></tr>
                <tr><th class="p-2">Job</th><td class="p-2">{{ $employee->job->name ?? '-' }}</td></tr>

                <tr><th class="p-2">Birthday</th><td class="p-2">{{ $employee->birthday }}</td></tr>
                <tr><th class="p-2">Initial Employment</th><td class="p-2">{{ $employee->initial_employment_date }}</td></tr>
                <tr><th class="p-2">Joining Date</th><td class="p-2">{{ $employee->joining_date }}</td></tr>
                <tr><th class="p-2">Permanent Date</th><td class="p-2">{{ $employee->permanent_date }}</td></tr>

                <tr><th class="p-2">Updated At</th><td class="p-2">{{ $employee->updated_at }}</td></tr>
            </table>

            <div class="flex gap-2 mb-4 text-center">
                <a href="{{ route('admin.employees.report.show', $employee) }}"
                class="bg-gray-800 text-white px-4 py-2 rounded">
                    View Learning Journey
                </a>

                <a href="{{ route('admin.employees.report.pdf', $employee) }}"
                class="bg-red-600 text-white px-4 py-2 rounded">
                    Download PDF
                </a>
            </div>


            <div class="mt-6 text-center">
                <a href="{{ route('admin.employees.index') }}"
                   class="text-blue-600 hover:underline">
                    ← Back to Employees
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
