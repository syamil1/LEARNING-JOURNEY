<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mentoring Detail — {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            
            <div class="bg-white p-6 shadow rounded border mb-6">
                <h3 class="text-lg font-semibold text-gray-800">{{ $employee->name }}</h3>
                <p class="text-gray-600">Employee ID: {{ $employee->employee_id }}</p>

                {{-- store --}}
                @php 
                    $store = DB::table('stores')->where('id', $employee->store_id)->first();
                @endphp

                <p class="text-gray-600 mb-2">Store: <span class="font-semibold">{{ $store->name ?? '-' }}</span></p>
            </div> 

            {{-- Riwayat mentoring --}}
            <div class="space-y-4">
                @foreach ($records as $index => $record)
                    <div class="bg-white p-5 shadow rounded border">

                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Mentoring #{{ $records->count() - $index }}
                            </h3>

                            <span class="px-3 py-1 text-xs rounded-full 
                                {{ $record->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                        </div>

                        <p class="text-gray-600 mt-2">
                            <span class="font-semibold">Mentor:</span>
                            {{ $record->mentor_name }}
                        </p> 

                        <div class="mt-3 bg-gray-50 p-4 rounded border text-gray-700">
                            {{ $record->notes }}
                        </div>

                        <p class="text-sm text-gray-500 mt-3">
                            Created: {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, H:i') }}
                        </p>

                        @if ($record->status == 'pending')
                            <form action="{{ route('admin.mentoring.verify', $record->id) }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        HR Notes
                                    </label>
                                    <textarea
                                        name="notes_hr"
                                        rows="3"
                                        class="w-full border rounded p-2 text-sm"
                                        placeholder="Notes from HR (optional)"></textarea>
                                </div>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                    Verify This Mentoring
                                </button>
                            </form>
                        @endif

                    </div>
                @endforeach
            </div>

        </div>
    </div>

</x-app-layout>
