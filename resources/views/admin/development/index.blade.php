<x-app-layout>
        <x-slot name="header">
            <h2 class="text-xl font-semibold">Employee Training Scores</h2>
        </x-slot>
        @if (session('success'))
            <div class="mb-4 p-4 rounded bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded bg-red-100 border border-red-300 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 p-4 rounded bg-yellow-100 border border-yellow-300 text-yellow-800">
                {{ session('warning') }}
            </div>
        @endif
        <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex items-center justify-between mb-4">

            <!-- SEARCH BOX -->
            <div class="w-80">
                <label class="font-semibold">Search Employee</label>

                <div class="relative mt-1">
                    <input                     type="text" 
                        id="employeeSearchIndex"
                        class="w-full border rounded px-4 py-2"
                        placeholder="Ketik Employee ID atau Nama..."
                    >
                    <div id="searchResultsIndex" class="mt-1 bg-white shadow rounded hidden absolute w-full z-50"></div>
                </div>
            </div>

            <div class="flex items-center gap-2">

                <button
                    type="button"
                    onclick="openImportModal()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Import CSV
                </button>

                <a href="{{ route('admin.development.create') }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-blue-700 transition">
                    + Add Training Score
                </a>
            </div>
        </div>


        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('employeeSearchIndex');
            const resultsBox = document.getElementById('searchResultsIndex');

            const SEARCH_URL = "{{ route('admin.search.employees') }}";

            async function fetchResults() {
                let q = input.value.trim();
                if (q.length < 1) {
                    resultsBox.innerHTML = "";
                    resultsBox.classList.add("hidden");
                    return;
                }

                try {
                    const res = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json' }
                    });

                    const data = await res.json();

                    if (!Array.isArray(data) || data.length === 0) {
                        resultsBox.innerHTML = "<p class='p-3 text-gray-500'>No results</p>";
                    } else {
                        resultsBox.innerHTML = data.map(item => `
                            <div class="p-2 border-b hover:bg-gray-100 cursor-pointer"
                                onclick="selectEmployeeIndex('${item.employee_id}', '${item.employee_id}', '${item.name}')">
                                <strong>${item.employee_id}</strong> - ${item.name}
                            </div>
                        `).join('');
                    }

                    resultsBox.classList.remove("hidden");

                } catch (err) {
                    console.error(err);
                }
            }

            input.addEventListener('keyup', fetchResults);
        });

        window.selectEmployeeIndex = function(employeeId) {
            window.location.href =
                "{{ route('admin.development.index') }}" + "?search=" + employeeId;
        };
        </script>


    <table class="min-w-full border text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Employee</th>
                <th class="p-2 border">Store</th>
                <th class="p-2 border">Gramedia Daily Store</th>
                <th class="p-2 border">Avg RSO Score</th>
                <th class="p-2 border">Learning Hours</th> 
                <th class="p-2 border">Nilai NGECAS</th>
                <th class="p-2 border">Actions</th>
            </tr>
        </thead>

    <tbody>
        @foreach ($scores as $score)
            <tr>
                <td class="border p-2">{{ $score->employee->name ?? '-' }}</td>
                <td class="border p-2">{{ $score->employee->store->name ?? '-' }}</td>
                <td class="border p-2">{{ $score->gramedia_daily_store }}</td>

                <!-- Rata-rata RSO -->
                <td class="border p-2">{{ $score->rso_average }}</td>

                <td class="border p-2">{{ $score->learning_hours }}</td>
                <td class="border p-2">{{ $score->nilai_ngecas }}</td>

                <td class="border p-2 flex gap-2">
                    <a href="{{ route('admin.development.show', $score->id) }}"
                        class="px-3 py-1 bg-blue-600 text-white rounded">View</a>

                    <a href="{{ route('admin.development.edit', $score->id) }}"
                        class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>

                    <form action="{{ route('admin.development.destroy', $score->id) }}"
                        method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
    <div class="mt-4">
        {{ $scores->links() }}
    </div>
    </div>
<!-- IMPORT CSV MODAL -->
<div id="importModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-md p-6 text-black">
        <h3 class="text-lg font-semibold mb-2">Import Training Score (CSV)</h3>

        <p class="text-sm text-gray-600 mb-4">
            Format CSV:
            employee_id, gramedia_daily_store, rso_supervisory_skill,
            rso_retail_salesmanship, rso_customer_service_loyalty,
            rso_product_merchandising, rso_visual_merchandising,
            rso_retail_store_promotion, rso_store_financial_perspective,
            rso_store_general_checkup_strategy, learning_hours,
            nilai_ngecas, compulsory_training, optional_training,
            development_program.
        </p>

        <a href="{{ asset('templates/Employee_Training_Score.csv') }}"
           download
           class="inline-block mb-3 text-sm text-blue-600 hover:underline">
            Download CSV Template
        </a>

        <form id="importForm"
              action="{{ route('admin.development.import') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <input
                type="file"
                name="file"
                accept=".csv"
                required
                class="border w-full px-3 py-2 rounded mb-4"
            >

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeImportModal()"
                    class="px-4 py-2 border rounded hover:bg-gray-100">
                    Cancel
                </button>

                <button
                    type="button"
                    onclick="confirmImport()"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importModal').classList.remove('flex');
}

function confirmImport() {
    document.getElementById('importForm').submit();
}
</script>

</x-app-layout>
