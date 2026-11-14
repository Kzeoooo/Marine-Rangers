@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'icon' => null,
    'helper' => null,
])

@php
    $hasError = $errors->has($name);
    $inputValue = old($name, $value);
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-[#21295C] mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-[#C0392B]">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-lg shadow-xs">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <x-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif

        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ $inputValue }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full min-h-[44px] rounded-lg border text-sm text-[#21295C] transition-colors duration-150 ' . 
                ($icon ? 'pl-11 ' : 'pl-3.5 ') . 'pr-3.5 py-2.5 ' .
                ($hasError 
                    ? 'border-[#C0392B] focus:border-[#C0392B] focus:ring-2 focus:ring-[#C0392B]/20 bg-[#FBE7E3]/20' 
                    : 'border-[#E3E8E6] bg-white hover:border-[#1C7293] focus:border-[#065A82] focus:ring-2 focus:ring-[#065A82]/20')
            ]) }}
        />
    </div>

    @if($hasError)
        <p class="mt-1.5 text-xs text-[#C0392B] font-medium flex items-center gap-1">
            <x-icon name="alert-circle" class="w-3.5 h-3.5 shrink-0" />
            {{ $errors->first($name) }}
        </p>
    @elseif($helper)
        <p class="mt-1.5 text-xs text-gray-500">{{ $helper }}</p>
    @endif
</div>
