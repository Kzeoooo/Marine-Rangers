@props([
    'fill' => '#FAFAF7',
    'opacity' => '1',
    'flip' => false,
    'animated' => true,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'w-full overflow-hidden leading-none ' . ($flip ? 'rotate-180 ' : '') . $class]) }} aria-hidden="true">
    <svg class="relative block w-full h-8 sm:h-12 md:h-16 lg:h-20 {{ $animated ? 'animate-wave-slow' : '' }}" 
         viewBox="0 0 1440 120" 
         preserveAspectRatio="none" 
         xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32L48,42.7C96,53,192,75,288,80C384,85,480,75,576,58.7C672,43,768,21,864,21.3C960,21,1056,43,1152,53.3C1248,64,1344,64,1392,64L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" 
              fill="{{ $fill }}" 
              fill-opacity="{{ $opacity }}"></path>
    </svg>
</div>
