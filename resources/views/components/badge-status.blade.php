@props([
    'status' => 'baru',
    'showMeaning' => true,
    'size' => 'md',
])

@php
    $normalized = strtolower(trim($status));

    $config = match($normalized) {
        'dipantau' => [
            'label' => 'Dipantau',
            'meaning' => 'Diteruskan ke pemantau',
            'color' => '#B8721A',
            'bg' => '#FCEFD9',
            'dot' => '#B8721A',
        ],
        'ditindaklanjuti' => [
            'label' => 'Ditindaklanjuti',
            'meaning' => 'Sedang ditangani',
            'color' => '#1C7293',
            'bg' => '#E6F1F0',
            'dot' => '#1C7293',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'meaning' => 'Selesai dibersihkan',
            'color' => '#2E8B57',
            'bg' => '#E3F2E8',
            'dot' => '#2E8B57',
        ],
        default => [
            'label' => 'Baru',
            'meaning' => 'Menunggu verifikasi',
            'color' => '#C0392B',
            'bg' => '#FBE7E3',
            'dot' => '#C0392B',
        ],
    };

    $sizeClasses = $size === 'sm' 
        ? 'px-2 py-0.5 text-xs' 
        : 'px-2.5 py-1 text-xs sm:text-sm';
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center gap-1.5 font-medium rounded-full $sizeClasses"
]) }} style="color: {{ $config['color'] }}; background-color: {{ $config['bg'] }};">
    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: {{ $config['dot'] }};"></span>
    <span class="font-semibold">{{ $config['label'] }}</span>
    @if($showMeaning)
        <span class="text-xs opacity-85">({{ $config['meaning'] }})</span>
    @endif
</span>
