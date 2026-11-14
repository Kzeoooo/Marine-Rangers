@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'image' => null, // e.g. 'images/pantai-kenjeran.jpg'
])

@php
    $hasImage = $image && file_exists(public_path($image));
@endphp

<div class="relative bg-gradient-to-r from-[#065A82] via-[#1C7293] to-[#21295C] text-white pt-10 pb-16 sm:pt-14 sm:pb-20 overflow-hidden">
    @if($hasImage)
        <img src="{{ asset($image) }}" 
             alt="{{ $title }}" 
             loading="lazy"
             class="absolute inset-0 w-full h-full object-cover z-0" />
    @else
        <!-- Ornamen Ombak SVG Transparan jika foto belum ada -->
        <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 1440 300">
                <path d="M0,96 C360,160 720,40 1080,120 C1260,160 1380,80 1440,100 L1440,300 L0,300 Z" fill="#CFE8E4" />
            </svg>
        </div>
    @endif

    <!-- Overlay Gradien Midnight -->
    <div class="absolute inset-0 bg-gradient-to-t from-[#21295C]/90 via-[#065A82]/80 to-[#065A82]/60 z-10"></div>

    <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            @if($badge)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-[#CFE8E4] mb-3">
                    <x-icon name="wave" class="w-3.5 h-3.5 text-[#E8735A]" />
                    <span>{{ $badge }}</span>
                </div>
            @endif

            <h1 class="font-serif text-2xl sm:text-4xl font-bold tracking-tight text-white mb-2">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="text-sm sm:text-base text-[#EAF4F8] leading-relaxed max-w-2xl opacity-90">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>

    <!-- Ombak di bagian bawah banner halaman -->
    <div class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none">
        <x-wave fill="#FAFAF7" :animated="false" class="h-6 sm:h-8" />
    </div>
</div>
