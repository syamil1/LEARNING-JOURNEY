<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Employee Evaluation</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

          <form method="GET" class="mb-4" id="evaluationSearchForm">
            <div class="flex items-end justify-between gap-4">

                {{-- LEFT: SEARCH --}}
                <div class="relative w-72">
                    <label class="block text-sm font-medium mb-1">Search Employee</label>

                    <input
                        type="text"
                        name="search"
                        id="employeeSearch"
                        value="{{ $search }}"
                        placeholder="Employee ID / Name..."
                        class="border px-3 py-2 rounded w-full text-black"
                        autocomplete="off"
                    >

                    <div
                        id="searchResults"
                        class="hidden absolute w-full bg-white border rounded shadow mt-1 z-50">
                    </div>
                </div>

                {{-- RIGHT: FILTER + SEARCH BUTTON --}}
                <div class="flex items-end gap-5">

                    {{-- SORT --}}
                    <div class="relative">
                        <label class="block text-sm font-medium mb-1 text-gray-700">
                            Sort By
                        </label>

                        <div class="relative">
                            <select
                                name="sort"
                                class="
                                    appearance-none
                                    w-[260px]
                                    border border-gray-300
                                    bg-white
                                    text-gray-800
                                    text-sm
                                    rounded-lg
                                    px-4 py-2.5
                                    pr-10
                                    shadow-sm
                                    focus:outline-none
                                    focus:ring-2 focus:ring-indigo-500
                                    focus:border-indigo-500
                                    transition
                                "
                            >
                                <option value="">Default</option>

                                <option value="kpi_june_desc" {{ request('sort')=='kpi_june_desc' ? 'selected' : '' }}>
                                    KPI June — Highest
                                </option>
                                <option value="kpi_june_asc" {{ request('sort')=='kpi_june_asc' ? 'selected' : '' }}>
                                    KPI June — Lowest
                                </option>

                                <option value="kpi_december_desc" {{ request('sort')=='kpi_december_desc' ? 'selected' : '' }}>
                                    KPI December — Highest
                                </option>
                                <option value="kpi_december_asc" {{ request('sort')=='kpi_december_asc' ? 'selected' : '' }}>
                                    KPI December — Lowest
                                </option>

                                <option value="development_desc" {{ request('sort')=='development_desc' ? 'selected' : '' }}>
                                    Avg Development — Highest
                                </option>
                                <option value="development_asc" {{ request('sort')=='development_asc' ? 'selected' : '' }}>
                                    Avg Development — Lowest
                                </option>
                            </select>

                            {{-- DROPDOWN ICON --}}
                            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- SEARCH BUTTON --}}
                    <button
                        type="submit"
                        class="
                            bg-gray-900
                            text-white
                            px-5 py-2.5
                            rounded-lg
                            text-sm
                            font-medium
                            shadow-sm
                            hover:bg-gray-800
                            focus:outline-none
                            focus:ring-2 focus:ring-offset-2 focus:ring-gray-900
                            transition
                        "
                    >
                        Search
                    </button>
                </div>


                {{-- ACTION BUTTONS --}}
                <div class="flex items-end gap-2">
                    <button
                        type="button"
                        onclick="openImportModal()"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Import Assesment | KPI
                    </button>

                    <a href="{{ route('admin.evaluations.export', request()->query()) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Export CSV
                    </a>

                    <a href="{{ route('admin.evaluations.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        + Add KPI
                    </a>
                </div>

            </div>
        </form>


            {{-- TABLE --}}
            <table class="w-full bg-white shadow rounded">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-3">Employee ID</th>
                        <th class="p-3">Employee Name</th>
                        <th class="p-3">Store</th>
                        <th class="p-3">Introduction</th>
                        <th class="p-3">6-Month Checklist</th>
                        <th class="p-3">KPI June</th>
                        <th class="p-3">KPI December</th>
                        <th class="p-3">Total Mentoring</th>
                        <th class="p-3">Avg Development</th>
                        <th class="p-3">Assessment</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($evaluations as $eval)
                        @php $emp = $eval->employee; @endphp
                        <tr class="border-b">
                            <td class="p-3">{{ $emp->employee_id }}</td>
                            <td class="p-3">{{ $emp->name }}</td>
                            <td class="p-3">{{ $emp->store->name ?? '-' }}</td>
                            <td class="p-3">{{ $eval->intro_status }}</td>
                            <td class="p-3">{{ $eval->checklist_summary }}</td>
                            <td class="p-3">{{ $eval->kpi_june }}</td>
                            <td class="p-3">{{ $eval->kpi_december }}</td>

                            <td class="p-3">
                                @if($eval->total_mentoring > 0)
                                    <span class="text-green-600 font-semibold">
                                        {{ $eval->total_mentoring }}x
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>

                            <td class="p-3">
                                @if($eval->avg_development)
                                    <span class="font-semibold">
                                        {{ $eval->avg_development }}
                                    </span>
                                @else
                                    <span class="text-red-500">Pending</span>
                                @endif
                            </td>

                            <td class="p-3">
                                @if($eval->assessment_link)
                                    <a href="{{ $eval->assessment_link }}"
                                       class="text-blue-600"
                                       target="_blank">
                                        View
                                    </a>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-3">
                                <a class="text-indigo-600"
                                   href="{{ route('admin.evaluations.edit', $eval->id) }}">
                                    Edit
                                </a>
                                |
                                <form action="{{ route('admin.evaluations.destroy', $eval->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600"
                                            onclick="return confirm('Delete?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $evaluations->links() }}
            </div>

        </div>
    </div>

    {{-- IMPORT KPI MODAL --}}
    <div id="importModal"
         class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-4">Import KPI CSV</h3>

            <form id="importForm"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    {{-- TEMPLATE DOWNLOAD --}}
                <div class="mb-4 space-y-2 text-sm">
                    <p class="text-gray-600 font-medium">Download Template:</p>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('admin.evaluations.template', 'kpi') }}"
                        class="px-3 py-2 border rounded hover:bg-gray-50 text-indigo-600">
                            ⬇ Template Import KPI
                        </a>

                        <a href="{{ route('admin.evaluations.template', 'assessment') }}"
                        class="px-3 py-2 border rounded hover:bg-gray-50 text-green-600">
                            ⬇ Template Import Assessment
                        </a>
                    </div>
                </div>

                    <label class="block text-sm font-medium mb-1">
                        Choose CSV File
                    </label>
                    <input type="file"
                           name="file"
                           required
                           class="w-full border px-3 py-2 rounded">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button"
                            onclick="closeImportModal()"
                            class="px-4 py-2 border rounded">
                        Cancel
                    </button>

                    <button type="button"
                            onclick="submitImport('june')"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Import KPI June
                    </button>

                    <button type="button"
                            onclick="submitImport('december')"
                            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        Import KPI December
                    </button>

                    <button type="button"
                            onclick="submitAssessmentImport()"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Import Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        function openImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function submitImport(period) {
            const form = document.getElementById('importForm');
            form.action = `/admin/employee-evaluations/import/${period}`;
            form.submit();
        }

        function submitAssessmentImport() {
            const form = document.getElementById('importForm');
            form.action = "{{ route('admin.employee-evaluations.import-assessment') }}";
            form.submit();
        }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('employeeSearch');
        const resultsBox = document.getElementById('searchResults');
        const form = document.getElementById('evaluationSearchForm');

        const SEARCH_URL = "{{ route('admin.search.employees') }}";

        async function fetchResults() {
            const q = input.value.trim();

            if (q.length < 1) {
                resultsBox.innerHTML = '';
                resultsBox.classList.add('hidden');
                return;
            }

            try {
                const res = await fetch(
                    SEARCH_URL + '?q=' + encodeURIComponent(q),
                    { headers: { 'Accept': 'application/json' } }
                );

                const data = await res.json();

                if (!Array.isArray(data) || data.length === 0) {
                    resultsBox.innerHTML =
                        "<div class='p-3 text-gray-500'>No results</div>";
                } else {
                    resultsBox.innerHTML = data.map(item => `
                        <div
                            class="p-2 border-b hover:bg-gray-100 cursor-pointer"
                            onclick="selectEmployee('${item.employee_id}')"
                        >
                            <strong>${item.employee_id}</strong> - ${item.name}
                        </div>
                    `).join('');
                }

                resultsBox.classList.remove('hidden');

            } catch (err) {
                console.error(err);
            }
        }

        input.addEventListener('keyup', fetchResults);

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });

        window.selectEmployee = function (employeeId) {
            input.value = employeeId;
            resultsBox.classList.add('hidden');
            form.submit();
        };
    });
    </script>

</x-app-layout>
