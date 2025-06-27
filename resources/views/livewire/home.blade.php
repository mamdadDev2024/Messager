<div class="max-w-2xl mx-auto p-4 space-y-4" x-data="{ showGroupModal: false, showUserModal: false }">
    <!-- جستجو و دکمه‌ها -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form wire:submit.prevent="search" class="flex flex-1 gap-2">
            <input type="text"
                wire:model.defer="search"
                placeholder="جستجو در گفتگوها..."
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all"/>
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-600 transition-all">
                جستجو
            </button>
        </form>

        <div class="flex gap-2 justify-end">
            <button @click="showUserModal = true"
                class="bg-blue-500 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-600 transition-all">
                جستجوی کاربر
            </button>

            <button @click="showGroupModal = true"
                class="bg-green-500 text-white px-4 py-2 rounded-xl text-sm hover:bg-green-600 transition-all">
                ساخت گروه
            </button>
        </div>
    </div>

    <!-- لیست گفتگوها -->
    <div class="bg-white rounded-xl shadow divide-y overflow-hidden">
        @if($chats->isEmpty())
            <div class="p-6 text-center text-gray-400 text-sm">گفتگویی یافت نشد.</div>
        @else
            @foreach($chats as $chat)
                <a href="{{ route('chat.show', $chat->id) }}"
                   class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-all duration-200">

                    <img src="{{ $chat->image->url ?? '/default.png' }}"
                         class="w-12 h-12 rounded-full object-cover border border-gray-300"/>

                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 truncate">{{ $chat->title ?? 'بدون عنوان' }}</div>
                        <div class="text-xs text-gray-500 truncate">
                            صاحب: {{ $chat->owner->username ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400 truncate">
                            {{ \Str::limit($chat->latestMessage->text ?? '', 50) }}
                        </div>
                    </div>

                    <span class="bg-gray-200 text-xs text-gray-600 rounded-full px-3 py-1 whitespace-nowrap">
                        {{ $chat->messages_count }} پیام
                    </span>
                </a>
            @endforeach
        @endif
    </div>

    <!-- مودال ساخت گروه -->
    <div x-show="showGroupModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         @click.away="showGroupModal = false"
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-bold mb-4 text-gray-700">ساخت گروه جدید</h2>
            <livewire:chat.create-group />
            <button @click="showGroupModal = false"
                    class="mt-6 w-full bg-gray-200 text-gray-700 rounded-lg py-2 hover:bg-gray-300 transition-all">
                بستن
            </button>
        </div>
    </div>

    <!-- مودال جستجوی کاربر -->
    <div x-show="showUserModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak
         @click.away="showUserModal = false"
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-bold mb-4 text-gray-700">جستجوی کاربر</h2>
            <livewire:user.search />
            <button @click="showUserModal = false"
                    class="mt-6 w-full bg-gray-200 text-gray-700 rounded-lg py-2 hover:bg-gray-300 transition-all">
                بستن
            </button>
        </div>
    </div>
</div>
