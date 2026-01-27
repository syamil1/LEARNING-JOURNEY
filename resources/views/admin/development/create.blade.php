<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Add Employee Training Score</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <form action="{{ route('admin.development.store') }}" method="POST" class="space-y-4">
            @csrf

    <div class="col-span-2">
        <label class="font-semibold">Search Employee</label>

        <div class="relative mt-1">
            <input 
                type="text" 
                id="employeeSearch"
                class="w-full border rounded px-4 py-2"
                placeholder="Ketik Employee ID atau Nama..."
            >
            <input type="hidden" name="employee_id" id="employeeIdInput" required>

            <button 
                id="searchBtn"
                class="absolute right-3 top-2 text-gray-600 hover:text-black">
                🔍
            </button>
        </div>

        <div id="searchResults" class="mt-2 bg-white shadow rounded hidden"></div>
    </div>

    @if ($errors->has('employee_id'))
        <p class="text-red-600 text-sm mt-1">
            {{ $errors->first('employee_id') }}
        </p>
    @endif


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('employeeSearch');
        const resultsBox = document.getElementById('searchResults');
        const btn = document.getElementById('searchBtn');

        const SEARCH_URL = "{{ route('admin.search-employees') }}";

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

                if (!res.ok) {
                    const txt = await res.text();
                    console.error('Search request failed:', res.status, txt);
                    resultsBox.innerHTML = "<p class='p-3 text-red-500'>Search error</p>";
                    resultsBox.classList.remove("hidden");
                    return;
                }

                const data = await res.json();

                if (!Array.isArray(data) || data.length === 0) {
                    resultsBox.innerHTML = "<p class='p-3 text-gray-500'>No results</p>";
                } else {
                    resultsBox.innerHTML = data.map(item => `
                        <div class="p-2 border-b hover:bg-gray-100 cursor-pointer"
                            onclick="selectEmployee('${item.employee_id}', '${item.name}')">
                            <strong>${item.employee_id}</strong> - ${item.name}
                        </div>
                    `).join('');
                }

                resultsBox.classList.remove("hidden");

            } catch (err) {
                console.error(err);
                resultsBox.innerHTML = "<p class='p-3 text-red-500'>Network error</p>";
                resultsBox.classList.remove("hidden");
            }
        }

        input.addEventListener('keyup', fetchResults);
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            fetchResults();
        });
    });

    // function to select result
    window.selectEmployee = function(employee_id, name) {
    document.getElementById('employeeSearch').value = employee_id + " - " + name;
    document.getElementById('employeeIdInput').value = employee_id;

        const resultsBox = document.getElementById('searchResults');
        resultsBox.classList.add("hidden");
        resultsBox.innerHTML = "";
    };
    </script>


            <hr>

            <h3 class="font-semibold text-lg">Scores (0–100)</h3>

            @php
                $fields = [
                    'gramedia_daily_store' => "Gramedia Daily's Store",
                    'rso_supervisory_skill' => 'RSO Supervisory Skill',
                    'rso_retail_salesmanship' => 'RSO Retail Salesmanship',
                    'rso_customer_service_loyalty' => 'RSO Customer Service & Loyalty',
                    'rso_product_merchandising' => 'RSO Product & Merchandising',
                    'rso_visual_merchandising' => 'RSO Visual Merchandising',
                    'rso_retail_store_promotion' => 'RSO Retail Store Promotion',
                    'rso_store_financial_perspective' => 'RSO Store Financial Perspective',
                    'rso_store_general_checkup_strategy' => 'RSO Store General Check Up & Strategy',
                    'learning_hours' => 'Learning Hours',
                    'nilai_ngecas' => 'Nilai NGECAS',
                ];
            @endphp

            @foreach ($fields as $name => $label)
                <div>
                    <label class="font-semibold">{{ $label }}</label>
                    <input type="number"
                           name="{{ $name }}"
                           class="w-full border rounded px-3 py-2"
                           min="0" max="100">
                </div>
            @endforeach

            <hr>

            <h3 class="font-semibold text-lg">Training Notes</h3>

            <div>
                <label>Compulsory Training</label>
                <textarea name="compulsory_training" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div>
                <label>Optional Training</label>
                <textarea name="optional_training" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div>
                <label>Development Program</label>
                <textarea name="development_program" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </form>
    </div>
</x-app-layout>
