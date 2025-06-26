<button
    type="{{ $type ?? 'submit' }}"
    {{ $attributes->merge([
        'class' => "p-2 m-1 transition-all cursor-pointer rounded-xl border shadow h-10 w-full max-w-xs bg-gray-200 hover:bg-gray-400"])
        }}>
    {{ $slot }}
</button>
