@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => 'Pilih opsi...',
    'options' => [],
    'value' => null,
    'helper' => null,
])

@php
    $hasError = $errors->has($name);
    $selectedValue = old($name, $value);
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
        <select 
            name="{{ $name }}" 
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full min-h-[44px] rounded-lg border text-sm text-[#21295C] transition-colors duration-150 pl-3.5 pr-10 py-2.5 appearance-none bg-white ' .
                ($hasError 
                    ? 'border-[#C0392B] focus:border-[#C0392B] focus:ring-2 focus:ring-[#C0392B]/20 bg-[#FBE7E3]/20' 
                    : 'border-[#E3E8E6] hover:border-[#1C7293] focus:border-[#065A82] focus:ring-2 focus:ring-[#065A82]/20')
            ]) }}
        >
            @if($placeholder)
                <option value="" disabled {{ is_null($selectedValue) ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif

            @if(count($options) > 0)
                @foreach($options as $val => $text)
                    <option value="{{ $val }}" {{ (string)$selectedValue === (string)$val ? 'selected' : '' }}>
                        {{ $text }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
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
