<button {{ $attributes->merge(['class'=>" p-2 m-1 hover:bg-gray-400 transition-all cursor-pointer rounded-xl border bg-gray-200 shadow h-10 w-full max-w-2xs"]) }}>
{{ $slot }}
</button>
