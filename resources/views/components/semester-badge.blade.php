@props(['semester' => null])

@if($semester)
    @php
        $num = $semester->semester_number;
        $classes = $num === 1
            ? 'bg-amber-50 text-amber-700 ring-amber-200'
            : 'bg-indigo-50 text-primary ring-indigo-200';
    @endphp
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold ring-1 {{ $classes }}">
        <span class="material-symbols-outlined text-[14px]">event_note</span>
        Semester {{ $num }}
        <span class="opacity-60 font-medium">{{ $semester->semester_type }}</span>
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-400 ring-1 ring-slate-200">
        Belum diatur
    </span>
@endif
