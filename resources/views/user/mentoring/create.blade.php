<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Add Mentoring</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <form method="POST" action="{{ route('user.mentoring.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="font-semibold">Employee</label>
                <select name="employee_id" class="w-full border rounded px-3 py-2">
                    @foreach ($employees as $e)
                        <option value="{{ $e->employee_id }}">{{ $e->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-semibold">Mentor Name</label>
                <input type="text" name="mentor_name"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="font-semibold">Notes</label>
                <textarea name="notes" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </form>
    </div>
</x-app-layout>
