<div class="h-screen flex flex-col bg-gray-100">

    <!-- Header -->
    <header class="flex items-center justify-between p-4 bg-white shadow-md">
        <div class="flex items-center gap-2">
            <img src="{{ $chat->image?->url ?? $chat->owner->avatar?->url ?? '/default-chat.png' }}"
                alt="Chat Image"
                class="w-10 h-10 rounded-full object-cover" />
            <h2 class="text-xl font-semibold truncate">{{ $chat->title ?? $chat->owner->username }}</h2>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('chat.leave') }}" method="POST" onsubmit="return confirm('از چت خارج شوید؟')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="chat_id" value="{{ $chat->id }}">
                <button class="text-sm text-red-500 hover:underline">ترک گفتگو</button>
            </form>
        </div>
    </header>

    <!-- Message List -->
    <main id="message-box" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 scroll-smooth">
        @foreach($chat->messages as $message)
            <div id="{{ $message->id }}"
                class="flex flex-col p-3 my-2 rounded-lg shadow text-sm max-w-xs w-fit
                    {{ $message->user_id === auth()->id() ? 'bg-green-100 self-end justify-end' : 'bg-blue-100 self-start justify-start' }}">
                <div class="font-bold text-gray-700">{{ $message->user->username }}</div>

                @if($message->attachment)
                    @php $type = $message->attachment->type; @endphp
                    @if(Str::startsWith($type, 'image/'))
                        <img src="{{ asset($message->attachment->url) }}"
                             class="rounded mb-1 max-h-48 cursor-pointer {{ !$message->attachment->visible ? 'blur-md' : '' }}"
                             title="برای نمایش کلیک کنید"
                             data-file-id="{{ $message->attachment->id }}" />
                    @else
                        <a href="{{ asset($message->attachment->url) }}"
                           class="text-blue-200 underline block mb-1 truncate"
                           target="_blank">{{ $message->attachment->file_name }}</a>
                    @endif
                @endif

                <div>{{ $message->text }}</div>
                <div class="text-[10px] text-gray-400 mt-1 text-end">
                    {{ $message->created_at->format('H:i') }}
                </div>
            </div>
        @endforeach
    </main>

    <!-- File Preview -->
    <section id="file-preview" class="px-4 py-2 hidden bg-white border-t">
        <p class="text-sm text-gray-600 mb-1">فایل انتخاب شده:</p>
        <div class="flex items-center gap-2">
            <span id="file-name" class="text-gray-800 text-sm truncate"></span>
            <button id="clear-file" class="text-red-500 hover:underline text-sm">حذف</button>
        </div>
    </section>

    <!-- Message Input -->
    <footer class="p-4 bg-white border-t flex items-center gap-2">
        <input type="file" id="file-input" class="hidden">
        <label for="file-input" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 7v10m0 0l5-5m-5 5l5 5m11-10v10m0 0l-5-5m5 5l-5 5"/>
            </svg>
        </label>

        <input type="text" id="message-input"
               placeholder="پیام خود را بنویسید..."
               class="flex-1 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400" />

        <button id="send-button"
                class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
            ارسال
        </button>
    </footer>

    <!-- Online Users -->
    <aside id="user-box"
           class="fixed right-0 top-0 h-full w-60 bg-white shadow-lg p-4 overflow-y-auto z-50">
        <h3 class="text-lg font-semibold mb-4 border-b pb-2">کاربران آنلاین</h3>
        <ul class="space-y-3" id="online-users-list">
            @foreach($chat->subscribers as $user)
                <li class="flex items-center gap-2" data-user-id="{{ $user->id }}">
                    <img src="{{ $user->avatar?->url ?? '/default-avatar.png' }}"
                         alt="avatar"
                         class="w-6 h-6 rounded-full object-cover">
                    <div class="flex flex-col">
                        <div class="font-medium text-sm">{{ $user->username }}</div>
                        <div class="text-xs text-gray-500" data-status>آنلاین</div>
                    </div>
                </li>
            @endforeach
        </ul>
    </aside>

</div>

@if(auth()->check())
    @push('scripts')
    <script type="module">
        const manager = new window.ChannelManager({
            id: @json($chat['id']),
            user: @json(auth()->user()->load('avatar')),
            messageBoxId: '#message-box',
            inputId: '#message-input',
            buttonId: '#send-button',
            userBoxId: '#user-box',
            baseUrl: '{{ env('APP_URL') }}',
            fileInputId: '#file-input',
            csrf: '{{ csrf_token() }}',
        });

        document.addEventListener('click', e => {
            if (e.target.matches('img.blur-md')) {
                e.target.classList.remove('blur-md');
                e.target.title = '';
            }
        });
    </script>
    @endpush
@endif
