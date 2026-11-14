@props([
    'type' => 'info',
    'message' => null,
])

@php
    $styles = [
        'success' => [
            'bg' => 'bg-[#E3F2E8]',
            'border' => 'border-[#2E8B57]',
            'text' => 'text-[#2E8B57]',
            'icon' => 'check',
        ],
        'error' => [
            'bg' => 'bg-[#FBE7E3]',
            'border' => 'border-[#C0392B]',
            'text' => 'text-[#C0392B]',
            'icon' => 'alert-circle',
        ],
        'warning' => [
            'bg' => 'bg-[#FCEFD9]',
            'border' => 'border-[#B8721A]',
            'text' => 'text-[#B8721A]',
            'icon' => 'alert-circle',
        ],
        'info' => [
            'bg' => 'bg-[#EAF4F8]',
            'border' => 'border-[#065A82]',
            'text' => 'text-[#065A82]',
            'icon' => 'wave',
        ],
    ];

    $style = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge([
    'class' => "flex items-start gap-3 p-4 rounded-lg border {$style['bg']} {$style['border']} {$style['text']} text-sm leading-relaxed"
]) }} role="alert">
    <div class="shrink-0 mt-0.5">
        <x-icon :name="$style['icon']" class="w-5 h-5" />
    </div>
    <div class="flex-1 font-medium">
        {{ $message ?? $slot }}
    </div>
</div>
