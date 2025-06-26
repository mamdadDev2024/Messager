<div x-data class="flex flex-col md:flex-row h-screen w-full bg-gray-100">
    <aside class="w-full md:w-64 bg-white border-r p-4 flex flex-col">
        <h2 class="text-lg font-semibold mb-4">کاربران حاضر</h2>
        <div id="user-box" class="space-y-2 text-sm text-gray-700 overflow-y-auto flex-1">
        </div>
        <a href="{{ route('home') }}" class="mt-4 px-3 py-1 bg-red-500 text-white rounded text-center">⬅ بازگشت</a>
    </aside>

    <main class="flex-1 flex flex-col p-4 overflow-hidden">
        <header class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-xl font-bold">گفتگو با {{ $chat['owner']->name }}</h1>
                <p class="text-sm text-gray-500">موضوع: {{ $chat['title'] ?? 'بدون عنوان' }}</p>
            </div>
        </header>
        <section id="message-box" class="flex-1 overflow-y-auto bg-white border rounded p-4 shadow">
            @foreach ($chat['messages'] as $message)
                <div id="{{ $message->id }}"
                     class="flex flex-col p-2 my-2 rounded shadow text-sm w-fit max-w-xs {{ $message->user_id === auth()->id() ? 'bg-green-100 self-end' : 'bg-blue-100 self-start' }}">
                    <div class="font-bold text-gray-700">{{ $message->user->username }}</div>
                    <div>{{ $message->text }}</div>

                    @if($message->attachment)
                        <div class="relative group mt-1" id="{{ $message->id }}">
                            @if (\Str::startsWith($message->attachment->type , 'image/'))
                                <img src="{{ asset($message->attachment->url) }}"
                                    class="max-w-full rounded-lg transition duration-300 {{ $message->attachment->visible < 0.6 ? 'blur-md' : '' }}"
                                    title="{{ $message->attachment->visible < 0.6 ? 'تصویر به دلیل محتوای نامناسب تار شده است.' : '' }}">
                            @else
                                <a href="{{ asset($message->attachment->url) }}"
                                class="text-blue-600 underline"
                                target="_blank"
                                download="{{ $message->attachment->file_name }}">
                                    دانلود فایل: {{ $message->attachment->file_name }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="text-gray-400 text-xs text-end">{{ $message->created_at }}</div>
                </div>
            @endforeach
        </section>

        <footer class="flex items-center gap-2 mt-4">
            <x-element.input id="message-input" type="text" placeholder="پیامت رو بنویس..." class="flex-1" />
            <input id="file-input" type="file" class="hidden" x-ref="file">
            <x-element.button @click="$refs.file.click()" class="bg-blue-500 text-white px-3 py-1 rounded">📎</x-element.button>
            <x-element.button id="send-button" class="bg-green-500 text-white px-3 py-1 rounded">ارسال</x-element.button>
        </footer>
    </main>
</div>

@if(auth()->check())
    @push('scripts')
    <script type="module">
        const manager = new window.ChannelManager({
            id: @json($chat['id']),
            user: @json(auth()->user()),
            messageBoxId: '#message-box',
            inputId: '#message-input',
            buttonId: '#send-button',
            userBoxId: '#user-box',
            baseUrl: '{{ env('APP_URL') }}',
            fileInputId: '#file-input',
            csrf: '{{ csrf_token() }}',
        });

        document.addEventListener('click', function (e) {
            if (e.target.tagName === 'IMG' && e.target.classList.contains('blur-md')) {
                e.target.classList.remove('blur-md');
                e.target.title = '';
            }
        });
    </script>
    @endpush
@endif
