@props([
    'type' => 'text',
    'placeholder' => '',
    'focusColor' => 'focus:ring-blue-500'
])

<input
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => "p-2 rounded-xl shadow-lg border w-full max-w-xs bg-gray-200 focus:outline-none focus:ring-1 transition-all {$focusColor}"
    ]) }}
>
