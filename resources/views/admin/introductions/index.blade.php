<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Interview & FGD Result
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- NOTIFICATION --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 bg-yellow-100 text-yellow-800 px-4 py-3 rounded">
                    {!! nl2br(e(session('warning'))) !!}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Search + Add Button --}} 
            <form method="GET" class="mb-4" id="introSearchForm">
                <div class="flex items-center justify-between">

        <div class="flex gap-3 items-end">

            <div class="relative">
                <input
                    type="text"
                    name="search"
                    id="introSearch"
                    value="{{ request('search') }}"
                    placeholder="Search NIK / Name..."
                    class="border px-3 py-2 rounded w-64"
                    autocomplete="off"
                >

                <div
                    id="introSearchResults"
                    class="hidden absolute w-full bg-white border rounded shadow mt-1 z-50">
                </div>
            </div>

            <button class="border border-gray-700 bg-gray-800 text-white px-4 py-2 rounded">
                Search
            </button>
        </div>

                    {{-- Add + Import --}}
                    <div class="flex gap-3">
                        <a href="{{ route('admin.introductions.create') }}"
                           class="border border-green-700 text-green-700 px-5 py-2 rounded hover:bg-green-700 hover:text-white transition">
                            Add Introduction
                        </a>
                        <button 
                            type="button"
                            onclick="openImportModal()"
                            class="border border-blue-700 text-blue-700 px-5 py-2 rounded hover:bg-blue-700 hover:text-white transition">
                            Import CSV
                        </button>
                    </div>
                </div>
            </form>

                        {{-- Table --}}
            <div class="bg-white shadow-md rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b bg-gray-100 text-left">
                            <th class="p-2">NIK</th>
                            <th class="p-2">Nama</th>
                            <th class="p-2">FGD</th>
                            <th class="p-2">Interview</th>
                            <th class="p-2">Joining Date</th>
                            <th class="p-2">PIC</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($introductions as $intro)
                        <tr class="border-b">
                            <td class="p-2">{{ $intro->nik }}</td>

                            {{-- Nama dari tabel employees --}}
                            <td class="p-2">{{ $intro->employee_name ?? '-' }}</td>

                            <td class="p-2">
                                @if($intro->fgd_average !== null)
                                    <div class="font-semibold">
                                        {{ $intro->fgd_average }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $intro->fgd_level_label ?? '-' }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="p-2">
                                @if($intro->interview_average !== null)
                                    <div class="font-semibold">
                                        {{ $intro->interview_average }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $intro->interview_level_label ?? '-' }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>


                            <td class="p-2">{{ $intro->joining_date ?? '-' }}</td>

                            <td class="p-2">{{ $intro->pic }}</td>

                            <td class="p-2 flex gap-2">
                                <a href="{{ route('admin.introductions.show', $intro->id) }}"
                                class="px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white">
                                    View
                                </a>

                                <a href="{{ route('admin.introductions.edit', $intro->id) }}"
                                class="px-3 py-1 border border-yellow-500 text-yellow-500 rounded hover:bg-yellow-500 hover:text-white">
                                    Edit
                                </a>

                                <form action="{{ route('admin.introductions.destroy', $intro->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


                <div class="mt-4">
                    {{ $introductions->links() }}
                </div>

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
        if (confirm('Are you sure you want to import this CSV data?')) {
            document.getElementById('importForm').submit();
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('introSearch');
        const resultsBox = document.getElementById('introSearchResults');
        const form = document.getElementById('introSearchForm');

        const SEARCH_URL = "{{ route('admin.search.employees') }}";

        async function fetchResults() {
            const q = input.value.trim();

            if (q.length < 1) {
                resultsBox.innerHTML = "";
                resultsBox.classList.add("hidden");
                return;
            }

            try {
                const res = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });

                const data = await res.json();

                if (!Array.isArray(data) || data.length === 0) {
                    resultsBox.innerHTML =
                        "<div class='p-3 text-gray-500'>No results</div>";
                } else {
                    resultsBox.innerHTML = data.map(item => `
                        <div
                            class="p-2 border-b hover:bg-gray-100 cursor-pointer"
                            onclick="selectIntro('${item.employee_id}')"
                        >
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

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add("hidden");
            }
        });

        window.selectIntro = function(value) {
            input.value = value;
            resultsBox.classList.add("hidden");
            form.submit();
        };
    });
    </script>

 <!-- IMPORT CSV MODAL -->
<div id="importModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-md p-6 text-black">
        <h3 class="text-lg font-semibold mb-2">Import Introduction (CSV)</h3>

        <p class="text-sm text-gray-600 mb-4">
            Format CSV:
            <br>
            <code class="text-xs">
                nik, fgd_analytic_score, fgd_business_score, fgd_leadership_score,
                interview_analytic_score, interview_business_score,
                interview_leadership_score, fgd_note, interview_note,
                mcu, psikotes, rekomendasi, pic
            </code>
        </p>

        {{-- Download template --}}
        <a href="{{ asset('templates/Introductions.csv') }}"
           download
           class="inline-block mb-3 text-sm text-blue-600 hover:underline">
            Download CSV Template
        </a>

        <form id="importForm"
              action="{{ route('admin.introductions.import') }}"
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


</x-app-layout>
