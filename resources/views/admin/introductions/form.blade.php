@php
    function levelText($score) {
        return match ((int) $score) {
            1 => 'Lone Ranger',
            2 => 'Team Player',
            3 => 'Team Leader',
            default => '-',
        };
    }

    $isShow = ($mode ?? '') === 'show';
@endphp

<div class="grid grid-cols-2 gap-4">

    {{-- ================= EMPLOYEE ================= --}}
    <div class="col-span-2">
        <label class="font-semibold">Employee</label>
        <input type="text"
               class="w-full border rounded px-4 py-2 bg-gray-100"
               value="{{ $introduction->employee->name ?? '-' }}"
               readonly>
    </div>

    {{-- ================= FGD ANALYTIC ================= --}}
    <div>
        <label>FGD Analytic Score</label>
        <input type="number" min="1" max="3"
               name="fgd_analytic_score"
               class="border p-2 rounded w-full"
               value="{{ old('fgd_analytic_score', $introduction->fgd_analytic_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>FGD Analytic Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->fgd_analytic_score) : '' }}">
    </div>

    {{-- ================= FGD BUSINESS ================= --}}
    <div>
        <label>FGD Business Score</label>
        <input type="number" min="1" max="3"
               name="fgd_business_score"
               class="border p-2 rounded w-full"
               value="{{ old('fgd_business_score', $introduction->fgd_business_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>FGD Business Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->fgd_business_score) : '' }}">
    </div>

    {{-- ================= FGD LEADERSHIP ================= --}}
    <div>
        <label>FGD Leadership Score</label>
        <input type="number" min="1" max="3"
               name="fgd_leadership_score"
               class="border p-2 rounded w-full"
               value="{{ old('fgd_leadership_score', $introduction->fgd_leadership_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>FGD Leadership Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->fgd_leadership_score) : '' }}">
    </div>

    {{-- ================= INTERVIEW ANALYTIC ================= --}}
    <div>
        <label>Interview Analytic Score</label>
        <input type="number" min="1" max="3"
               name="interview_analytic_score"
               class="border p-2 rounded w-full"
               value="{{ old('interview_analytic_score', $introduction->interview_analytic_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>Interview Analytic Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->interview_analytic_score) : '' }}">
    </div>

    {{-- ================= INTERVIEW BUSINESS ================= --}}
    <div>
        <label>Interview Business Score</label>
        <input type="number" min="1" max="3"
               name="interview_business_score"
               class="border p-2 rounded w-full"
               value="{{ old('interview_business_score', $introduction->interview_business_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>Interview Business Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->interview_business_score) : '' }}">
    </div>

    {{-- ================= INTERVIEW LEADERSHIP ================= --}}
    <div>
        <label>Interview Leadership Score</label>
        <input type="number" min="1" max="3"
               name="interview_leadership_score"
               class="border p-2 rounded w-full"
               value="{{ old('interview_leadership_score', $introduction->interview_leadership_score) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

    <div>
        <label>Interview Leadership Level</label>
        <input type="text" class="border p-2 rounded w-full bg-gray-100" readonly
               value="{{ $isShow ? levelText($introduction->interview_leadership_score) : '' }}">
    </div>

    {{-- ================= NOTES ================= --}}
    <div class="col-span-2">
        <label>FGD Note</label>
        <textarea class="border p-2 rounded w-full" {{ $isShow ? 'readonly' : '' }}>
{{ old('fgd_note', $introduction->fgd_note) }}</textarea>
    </div>

    <div class="col-span-2">
        <label>Interview Note</label>
        <textarea class="border p-2 rounded w-full" {{ $isShow ? 'readonly' : '' }}>
{{ old('interview_note', $introduction->interview_note) }}</textarea>
    </div>

    {{-- ================= FINAL ================= --}}
    <div class="col-span-2">
        <label>Rekomendasi</label>
        <textarea class="border p-2 rounded w-full" {{ $isShow ? 'readonly' : '' }}>
{{ old('rekomendasi', $introduction->rekomendasi) }}</textarea>
    </div>

    <div>
        <label>PIC</label>
        <input type="text" class="border p-2 rounded w-full"
               value="{{ old('pic', $introduction->pic) }}"
               {{ $isShow ? 'readonly' : '' }}>
    </div>

</div>
