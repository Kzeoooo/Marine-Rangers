@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 min-h-[44px] px-5 py-2.5 text-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none active:scale-[0.98]';

    $variants = [
        'primary' => 'bg-[#065A82] text-white hover:bg-[#054868] focus:ring-[#065A82] shadow-sm hover:shadow-md',
        'secondary' => 'bg-[#E8735A] text-white hover:bg-[#d6624a] focus:ring-[#E8735A] shadow-sm hover:shadow-md',
        'teal' => 'bg-[#1C7293] text-white hover:bg-[#165a74] focus:ring-[#1C7293] shadow-sm hover:shadow-md',
        'outline' => 'bg-transparent text-[#065A82] border-2 border-[#065A82] hover:bg-[#EAF4F8] focus:ring-[#065A82]',
        'danger' => 'bg-[#C0392B] text-white hover:bg-[#a93226] focus:ring-[#C0392B] shadow-sm',
        'ghost' => 'bg-transparent text-[#21295C] hover:bg-[#EAF4F8] focus:ring-[#065A82]',
        'white' => 'bg-white text-[#065A82] hover:bg-[#FAFAF7] focus:ring-white shadow-sm font-semibold',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
