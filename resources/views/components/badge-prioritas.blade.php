@props([
    'prioritas' => 'sedang',
    'skor' => null,
    'size' => 'sm',
])

@php
    $normalized = strtolower(trim($prioritas));

    $config = match($normalized) {
        'tinggi' => [
            'label' => 'Tinggi',
            'color' => 'text-rose-700',
            'bg' => 'bg-rose-50',
            'border' => 'border-rose-200',
            'dot' => 'bg-rose-500',
        ],
        'rendah' => [
            'label' => 'Rendah',
            'color' => 'text-emerald-700',
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        default => [
            'label' => 'Sedang',
            'color' => 'text-amber-700',
            'bg' => 'bg-amber-50',
            'border' => 'border-amber-200',
            'dot' => 'bg-amber-500',
        ],
    };

    $sizeClasses = $size === 'sm' 
        ? 'px-2 py-0.5 text-[10px]' 
        : 'px-2.5 py-1 text-xs';
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center gap-1.5 font-semibold rounded-full border {$config['color']} {$config['bg']} {$config['border']} $sizeClasses"
]) }}>
    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
    @if($skor !== null)
        <span class="text-[9px] opacity-75 font-mono">({{ $skor }})</span>
    @endif
</span>
