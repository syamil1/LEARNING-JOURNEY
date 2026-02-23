<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Interview & FGD Result</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto px-6 py-6">

        {{-- NOTIFICATION --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded bg-green-100 border border-green-300 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- 🔎 SEARCH + ACTION --}}
        <form method="GET" class="mb-4">
            <div class="flex items-center justify-between gap-4">

                {{-- SEARCH --}}
                <div class="relative w-72">
                    <label class="font-semibold">Search Employee</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Employee ID / Name..."
                        class="w-full border rounded px-3 py-2 mt-1"
                    >
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.introductions.create') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        + Add Introduction
                    </a>

                    <button type="button"
                        onclick="openImportModal()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Import CSV
                    </button>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-3 py-2 text-left">NIK</th>
                            <th class="px-3 py-2 text-left">Name</th>
                            <th class="px-3 py-2 text-left">FGD</th>
                            <th class="px-3 py-2 text-left">Interview</th>
                            <th class="px-3 py-2 text-left">Joining</th>
                            <th class="px-3 py-2 text-left">PIC</th>
                            <th class="px-3 py-2 text-left w-20">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                    @foreach($introductions as $intro)
                    <tr
                        onclick="window.location='{{ route('admin.introductions.edit', $intro->id) }}'"
                        class="cursor-pointer hover:bg-blue-50 transition group"
                    >
                        {{-- NIK --}}
                        <td class="px-3 py-2 align-middle h-[56px] font-medium group-hover:text-blue-700">
                            {{ $intro->nik }}
                        </td>

                        {{-- NAME --}}
                        <td class="px-3 py-2 align-middle h-[56px]">
                            <span class="group-hover:text-blue-700 group-hover:underline">
                                {{ $intro->employee_name ?? '-' }}
                            </span>
                        </td>

                        {{-- FGD --}}
                        <td class="px-3 py-2 align-middle h-[56px]">
                            <div class="font-semibold text-indigo-600">
                                {{ $intro->fgd_average ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $intro->fgd_level_label ?? ' ' }}
                            </div>
                        </td>

                        {{-- INTERVIEW --}}
                        <td class="px-3 py-2 align-middle h-[56px]">
                            <div class="font-semibold text-indigo-600">
                                {{ $intro->interview_average ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $intro->interview_level_label ?? ' ' }}
                            </div>
                        </td>

                        <td class="px-3 py-2 align-middle h-[56px]">
                            {{ $intro->joining_date ?? '-' }}
                        </td>

                        <td class="px-3 py-2 align-middle h-[56px]">
                            {{ $intro->pic ?? '-' }}
                        </td>

                        {{-- DELETE --}}
                        <td class="px-3 py-2 align-middle h-[56px]" onclick="event.stopPropagation();">
                            <form action="{{ route('admin.introductions.destroy', $intro->id) }}"
                                method="POST"
                                onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t">
                {{ $introductions->links() }}
            </div>
        </div>
    </div>

    {{-- 🔥 IMPORT MODAL --}}
    <div id="importModal"
         class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-semibold mb-2">Import Introduction (CSV)</h3>

            <p class="text-sm text-gray-600 mb-4">
                Format CSV: nik, fgd_score, interview_score, pic
            </p>

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

                <input type="file"
                       name="file"
                       accept=".csv"
                       required
                       class="border w-full px-3 py-2 rounded mb-4">

                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="closeImportModal()"
                            class="px-4 py-2 border rounded hover:bg-gray-100">
                        Cancel
                    </button>

                    <button type="button"
                            onclick="confirmImport()"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 🔥 MODAL SCRIPT --}}
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

        function confirmImport() {
            if (confirm('Import this CSV data?')) {
                document.getElementById('importForm').submit();
            }
        }
    </script>
</x-app-layout>