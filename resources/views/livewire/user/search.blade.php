<div>
    <input type="text" wire:model.lazy="query" placeholder="نام کاربر..." class="w-full rounded border px-3 py-2 mb-2 focus:ring-2 focus:ring-blue-400 transition-all">
    <ul class="divide-y">
        @forelse($results as $user)
            <li class="flex items-center justify-between p-2">
                <span>{{ $user->username }}</span>
                <button wire:click="startChat({{ $user->id }})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">شروع گفتگو</button>
            </li>
        @empty
            <li class="p-2 text-gray-400 text-center">کاربری یافت نشد.</li>
        @endforelse
    </ul>
</div>
