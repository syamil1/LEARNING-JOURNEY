<x-app-layout>
<div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

@forelse($employees as $employee)
<div class="bg-white rounded-xl border shadow">

<button onclick="toggle('emp-{{ $employee->employee_id }}')"
        class="w-full p-4 font-semibold bg-gray-100 rounded-t-xl">
    {{ $employee->name }}
</button>

<div id="emp-{{ $employee->employee_id }}" class="hidden p-4 space-y-3">

@foreach($employee->months as $month)
<div class="border rounded-lg">

<button onclick="toggle('month-{{ $employee->employee_id }}-{{ $month['month'] }}')"
    class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100">

    <span>Month {{ $month['month'] }}</span>

<span class="text-xs px-2 py-1 rounded-full
    {{ $month['status'] === 'approved' ? 'bg-green-100 text-green-700' :
       ($month['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-700' :
       ($month['status'] === 'rejected' ? 'bg-red-100 text-red-700' :
       'bg-gray-100 text-gray-600')) }}">
    {{ ucfirst(str_replace('_',' ',$month['status'])) }}
</span>
</button>

<div id="month-{{ $employee->employee_id }}-{{ $month['month'] }}"
     class="hidden px-4 py-3 space-y-2">

@foreach($month['weeks'] as $week)
<a href="{{ route('user.onboarding.checklist.show', [
        $employee->employee_id,
        $month['month'],
        $week['week']
    ]) }}"
   class="flex justify-between items-center p-2 rounded border hover:bg-gray-50">

    <span>Week {{ $week['week'] }}</span>

    <span class="text-xs {{ $week['filled'] ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
        {{ $week['filled'] ? 'Filled' : 'Empty' }}
    </span>
</a>
@endforeach

</div>
</div>
@endforeach

</div>
</div>
@empty
<p class="text-gray-500">No employee found.</p>
@endforelse

</div>

<script>
function toggle(id){
    document.getElementById(id)?.classList.toggle('hidden')
}
</script>
</x-app-layout>
