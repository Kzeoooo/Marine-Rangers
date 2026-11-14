@props([
    'hover' => false,
    'padding' => 'p-6',
    'class' => '',
])

<div {{ $attributes->merge([
    'class' => 'bg-white rounded-[12px] border border-[#E3E8E6] shadow-xs ' . 
               ($hover ? 'transition-all duration-200 hover:shadow-md hover:border-[#1C7293]/40 hover:-translate-y-0.5 ' : '') .
               $padding . ' ' . $class
]) }}>
    {{ $slot }}
</div>
