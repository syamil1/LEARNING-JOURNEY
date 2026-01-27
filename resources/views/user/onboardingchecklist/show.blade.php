<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl">
        Month {{ $month }} – Week {{ $week }}
    </h2>
</x-slot>

<div class="max-w-4xl mx-auto py-6 space-y-6">

{{-- NOTES HR (READ ONLY) --}}
@if(!empty($checklist->notes_hr))
<div class="bg-yellow-50 border border-yellow-300 p-4 rounded">
    <h3 class="font-semibold text-yellow-800 mb-1">Notes from HR</h3>
    <p class="text-yellow-700">{{ $checklist->notes_hr }}</p>
</div>
@endif

@php
    $readonly = $checklist->status === 'approved';
@endphp

<form method="POST"
    action="{{ $readonly ? '#' : route('user.onboarding.checklist.store', [$checklist->employee_id, $month, $week]) }}"
    class="space-y-6">
@csrf

{{-- TITLE --}}
<h1 class="text-2xl font-bold">
    {{ $data['title'] ?? 'Checklist' }}
</h1>

{{-- CHECKLIST ITEMS --}}
@if(!empty($data['items']))
<div>
    <h3 class="font-semibold mb-2">Checklist Items</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($data['items'] as $i => $item)
        <label class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
            <input type="checkbox"
                name="checklist[items][{{ $i }}][checked]"
                value="1"
                {{ !empty($item['checked']) ? 'checked' : '' }}
                {{ $readonly ? 'disabled' : '' }}>

            <input type="hidden"
                name="checklist[items][{{ $i }}][text]"
                value="{{ $item['text'] }}">

            <span>{{ $item['text'] }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- MANDATORY TASKS --}}
@if(!empty($data['mandatory_tasks']))
<div>
    <h3 class="font-semibold mb-2">Mandatory Tasks</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($data['mandatory_tasks'] as $i => $task)
        <label class="flex items-center gap-3 bg-gray-50 p-3 rounded border">
            <input type="checkbox"
                name="checklist[mandatory_tasks][{{ $i }}][checked]"
                value="1"
                {{ !empty($task['checked']) ? 'checked' : '' }}
                {{ $readonly ? 'disabled' : '' }}>

            <input type="hidden"
                name="checklist[mandatory_tasks][{{ $i }}][text]"
                value="{{ $task['text'] }}">

            <span>{{ $task['text'] }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- FILLED BY --}}
<div>
    <label class="font-semibold">Filled By</label>
    <input type="text"
        name="filled_by"
        required
        class="w-full border rounded p-2"
        value="{{ old('filled_by', $checklist->filled_by) }}"
        {{ $readonly ? 'readonly' : '' }}>
</div>

{{-- NOTES STORE MANAGER --}}
<div>
    <label class="font-semibold">Notes Store Manager</label>
    <textarea name="notes_store_manager"
        rows="4"
        class="w-full border rounded p-2"
        {{ $readonly ? 'readonly' : '' }}>{{ old('notes_store_manager', $checklist->notes_store_manager) }}</textarea>
</div>

@if(!$readonly)
<button class="bg-blue-600 text-white px-4 py-2 rounded">
    Save Checklist
</button>
@else
<div class="text-gray-500 font-medium mt-2">
    This checklist has been approved and is read-only.
</div>
@endif

</form>
</div>
</x-app-layout>
