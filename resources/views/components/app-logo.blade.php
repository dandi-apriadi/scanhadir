@props([
    'size' => 'w-10 h-10',
    'alt' => 'Logo SMP Kristen Rurukan',
])

<img
    src="{{ asset('images/logo.jpeg') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => $size . ' rounded-full object-cover bg-white shadow-sm ring-1 ring-slate-200/70']) }}
/>
