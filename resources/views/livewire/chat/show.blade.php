<div class="h-screen flex flex-col lg:flex-row bg-gray-50 text-gray-800">

  <!-- Left Sidebar -->
  <aside class="hidden lg:flex lg:w-1/5 bg-white border-r p-6 flex-col shadow-inner animate-slide-in-left">
    <h3 class="text-lg font-semibold mb-4 text-blue-700">📌 درباره گفتگو</h3>
    <div class="flex flex-col gap-3 text-sm text-gray-700 leading-relaxed">
      <div><span class="font-bold">عنوان:</span> {{ $chat->title ?? 'بدون عنوان' }}</div>
      <div><span class="font-bold">مالک:</span> {{ $chat->owner->username }}</div>
      <div><span class="font-bold">تاریخ ساخت:</span> {{ $chat->created_at->format('Y/m/d') }}</div>

      <form action="{{ route('chat.leave') }}" method="POST" class="mt-5">
        @csrf @method('DELETE')
        <input type="hidden" name="chat_id" value="{{ $chat->id }}">
        <button class="w-full py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-red-400">🚪 ترک گفتگو</button>
      </form>
    </div>
  </aside>

  <!-- Chat Column -->
  <div class="flex-1 flex flex-col relative">

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 bg-white border-b shadow-sm sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <img src="{{ $chat->image?->url ?? $chat->owner->avatar?->url ?? '/default-chat.png' }}"
             alt="Chat Image"
             class="w-12 h-12 rounded-full object-cover ring-2 ring-blue-400 hover:scale-105 transform duration-200 shadow-sm" />
        <h2 class="text-xl font-bold truncate text-gray-800">{{ $chat->title ?? $chat->owner->username }}</h2>
      </div>
    </header>

    <!-- Messages -->
    <main id="message-box" class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 space-y-5 scroll-smooth">
      @foreach($chat->messages as $message)
        <div id="message-{{ $message->id }}"
            class="max-w-2xl w-fit p-4 rounded-2xl shadow transition-all duration-300 transform animate-fade-in
            {{ $message->user_id === auth()->id() ? 'ml-auto bg-green-50' : 'mr-auto bg-blue-50' }}">

          <div class="flex items-center gap-2 justify-between mb-2 text-xs text-gray-500">
            <span class="font-medium text-gray-700">{{ $message->user->username }}</span>
            <span class="text-[10px] text-gray-400 mt-1 text-end">{{ $message->created_at->format('H:i') }}</span>
          </div>

          @if($message->attachment)
            @php $type = $message->attachment->type; @endphp
            @if(Str::startsWith($type, 'image/'))
              <img src="{{ asset($message->attachment->url) }}"
                   class="rounded-xl mb-3 max-h-60 w-full object-contain cursor-pointer hover:scale-105 transition-transform duration-200 {{ !$message->attachment->visible ? 'blur-sm' : '' }}"
                   title="برای نمایش کلیک کنید"
                   data-file-id="{{ $message->attachment->id }}" />
            @else
              <a href="{{ asset($message->attachment->url) }}"
                 class="inline-block text-blue-600 underline mb-3 truncate"
                 target="_blank">{{ $message->attachment->file_name }}</a>
            @endif
          @endif

          <p class="text-base leading-relaxed text-gray-800 break-words">{{ $message->text }}</p>
        </div>
      @endforeach
    </main>

    <footer class="px-4 md:px-6 py-4 bg-white border-t flex items-center gap-3">
        <input type="file" id="file-input" class="hidden">
        <label for="file-input" class="p-2 rounded-full hover:bg-gray-100 cursor-pointer transition-colors">
          📎
        </label>
        <input type="text" id="message-input"
               placeholder="پیام خود را بنویسید..."
               class="flex-1 px-5 py-2 md:py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition" />
        <button id="send-button"
                class="bg-blue-500 text-white px-6 py-2 md:py-3 rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all">
          ارسال
        </button>
    </footer>

  </div>

  <aside class="hidden xl:flex xl:w-1/5 bg-white border-l p-6 flex-col shadow-inner overflow-y-auto animate-slide-in-right">
    <h3 class="text-lg font-semibold mb-4 text-green-700">🟢 کاربران آنلاین</h3>
    <ul class="space-y-4" id='user-box'>
      @foreach($chat->subscribers as $user)
        <li class="flex items-center gap-3" data-user-id="{{ $user->id }}">
          <img src="{{ $user->avatar?->url ?? '/default-avatar.png' }}"
               alt="User Avatar"
               class="w-10 h-10 rounded-full object-cover ring-1 ring-gray-200 shadow-sm" />
          <div>
            <div class="font-medium">{{ $user->username }}</div>
            <div class="text-xs text-gray-500" data-status>آفلاین</div>
          </div>
        </li>
      @endforeach
    </ul>
  </aside>

</div>

@push('scripts')
<script type="module">
    const manager = new window.ChannelManager({
        id: @json($chat->id),
        user: @json(auth()->user()->load('avatar')),
        messageBoxId: '#message-box',
        inputId: '#message-input',
        buttonId: '#send-button',
        userBoxId: '#user-box',
        fileInputId: '#file-input',
        csrf: '{{ csrf_token() }}',
        baseUrl: '{{ url('/') }}'
    });

    document.addEventListener('click', e => {
        if (e.target.matches('img.blur-sm')) {
            e.target.classList.remove('blur-sm');
            e.target.title = '';
        }
    });
</script>
@endpush
