<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Edit Training Score</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto bg-white p-6 shadow rounded">
        
        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- UPDATE FORM --}}
        <form action="{{ route('admin.development.update', $score->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- EMPLOYEE --}}
            <div class="mb-4">
                <label class="font-semibold">Employee</label>

                {{-- Select disable agar tidak bisa dipilih --}}
                <select class="w-full border rounded p-2 bg-gray-100" disabled>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->employee_id }}"
                            {{ $emp->employee_id == $score->employee_id ? 'selected' : '' }}>
                            {{ $emp->employee_id }} - {{ $emp->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Hidden input agar tetap terkirim ke controller --}}
                <input type="hidden" name="employee_id" value="{{ $score->employee_id }}">
            </div>


            {{-- TRAINING SCORES --}}
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    'gramedia_daily_store' => 'Gramedia Daily Store',
                    'rso_supervisory_skill' => 'Supervisory Skill',
                    'rso_retail_salesmanship' => 'Retail Salesmanship',
                    'rso_customer_service_loyalty' => 'Customer Service Loyalty',
                    'rso_product_merchandising' => 'Product Merchandising',
                    'rso_visual_merchandising' => 'Visual Merchandising',
                    'rso_retail_store_promotion' => 'Retail Store Promotion',
                    'rso_store_financial_perspective' => 'Financial Perspective',
                    'rso_store_general_checkup_strategy' => 'General Checkup Strategy',
                    'learning_hours' => 'Learning Hours',
                    'nilai_ngecas' => 'Nilai NGECAS'
                ] as $field => $label)

                    <div>
                        <label class="font-semibold">{{ $label }}</label>
                        <input type="number"
                            name="{{ $field }}"
                            value="{{ old($field, $score->$field) }}"
                            class="w-full border rounded p-2">
                    </div>

                @endforeach
            </div>


            {{-- TEXT FIELDS --}}
            <div class="mt-4">
                <label class="font-semibold">Compulsory Training</label>
                <textarea
                    name="compulsory_training"
                    rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('compulsory_training', $score->compulsory_training) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="font-semibold">Optional Training</label>
                <textarea
                    name="optional_training"
                    rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('optional_training', $score->optional_training) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="font-semibold">Development Program</label>
                <textarea
                    name="development_program"
                    rows="3"
                    class="w-full border rounded px-3 py-2">{{ old('development_program', $score->development_program) }}</textarea>
            </div>

            {{-- UPDATE BUTTON --}}
            <div class="flex justify-end mt-6">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update 
                </button>
            </div>

        </form>

        {{-- DELETE BUTTON (di luar form update) --}}
        <div class="mt-4 flex justify-end">
            <form action="{{ route('admin.development.destroy', $score->id) }}"
                  method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this record?')">
                @csrf
                @method('DELETE')

                <button class="px-4 py-2 bg-red-600 text-white rounded">
                    Delete
                </button>
            </form>
        </div>
 
    </div>
</x-app-layout>
