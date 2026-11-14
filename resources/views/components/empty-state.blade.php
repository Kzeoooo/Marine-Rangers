@props([
    'title' => 'Belum Ada Data',
    'description' => 'Saat ini belum ada laporan atau aktivitas yang tercatat.',
    'icon' => 'net',
    'actionText' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center p-8 sm:p-12 bg-white rounded-[12px] border border-[#E3E8E6]']) }}>
    <div class="w-16 h-16 rounded-full bg-[#EAF4F8] text-[#065A82] flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="w-8 h-8" />
    </div>

    <h3 class="text-lg font-serif font-semibold text-[#21295C] mb-1.5">
        {{ $title }}
    </h3>

    <p class="text-sm text-gray-500 max-w-md mb-6 leading-relaxed">
        {{ $description }}
    </p>

    @if($actionText && $actionHref)
        <x-button :href="$actionHref" variant="primary">
            {{ $actionText }}
        </x-button>
    @endif

    {{ $slot }}
</div>
