@props([
    'status' => 'baru',
])

@php
    $steps = [
        1 => [
            'key' => 'baru',
            'title' => 'Baru',
            'meaning' => 'Menunggu verifikasi',
            'icon' => 'camera',
            'color' => '#C0392B',
            'bg' => '#FBE7E3',
        ],
        2 => [
            'key' => 'dipantau',
            'title' => 'Dipantau',
            'meaning' => 'Diteruskan ke pemantau',
            'icon' => 'shield-check',
            'color' => '#B8721A',
            'bg' => '#FCEFD9',
        ],
        3 => [
            'key' => 'ditindaklanjuti',
            'title' => 'Ditindaklanjuti',
            'meaning' => 'Sedang ditangani',
            'icon' => 'net',
            'color' => '#1C7293',
            'bg' => '#E6F1F0',
        ],
        4 => [
            'key' => 'selesai',
            'title' => 'Selesai',
            'meaning' => 'Selesai dibersihkan',
            'icon' => 'check',
            'color' => '#2E8B57',
            'bg' => '#E3F2E8',
        ],
    ];

    $currentIndex = match(strtolower(trim($status))) {
        'dipantau' => 2,
        'ditindaklanjuti' => 3,
        'selesai' => 4,
        default => 1,
    };
@endphp

<div class="w-full py-4">
    <div class="relative">
        <!-- Connecting Line Background -->
        <div class="hidden sm:block absolute top-5 left-8 right-8 h-1 bg-[#E3E8E6] -z-0">
            <!-- Progress Line -->
            <div class="h-full bg-[#065A82] transition-all duration-500" 
                 style="width: {{ (($currentIndex - 1) / 3) * 100 }}%;"></div>
        </div>

        <!-- Step Items -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-2 relative z-10">
            @foreach($steps as $index => $step)
                @php
                    $isPassed = $index < $currentIndex;
                    $isActive = $index === $currentIndex;
                    $isFuture = $index > $currentIndex;
                @endphp

                <div class="flex sm:flex-col items-center gap-3 sm:gap-2 text-left sm:text-center">
                    <!-- Circle Indicator -->
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-sm transition-all duration-300 shadow-xs
                        @if($isPassed)
                            bg-[#065A82] text-white ring-4 ring-[#EAF4F8]
                        @elseif($isActive)
                            text-white ring-4 ring-offset-2
                        @else
                            bg-white text-gray-400 border-2 border-[#E3E8E6]
                        @endif
                    " 
                    @if($isActive) 
                        style="background-color: {{ $step['color'] }}; --tw-ring-color: {{ $step['bg'] }};" 
                    @endif>
                        @if($isPassed)
                            <x-icon name="check" class="w-5 h-5" />
                        @else
                            <x-icon :name="$step['icon']" class="w-5 h-5" />
                        @endif
                    </div>

                    <!-- Step Text -->
                    <div class="flex-1 sm:w-full">
                        <div class="text-xs uppercase tracking-wider font-semibold 
                            @if($isActive) text-[#21295C] @elseif($isPassed) text-[#065A82] @else text-gray-400 @endif">
                            Langkah {{ $index }}: {{ $step['title'] }}
                        </div>
                        <div class="text-xs sm:text-[13px] font-medium leading-snug
                            @if($isActive) font-bold text-[#21295C] @elseif($isPassed) text-gray-600 @else text-gray-400 @endif">
                            {{ $step['meaning'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
