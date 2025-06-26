<div>
    <div x-data class="flex flex-col gap-2 p-4">
        <div id="message-box" class="h-96 overflow-y-auto border p-2 bg-white rounded shadow"></div>

        <div class="flex items-center gap-2">
            <x-element.input id="message-input" type="text" placeholder="پیامت رو بنویس..." />
            <input id="file-input" type="file" class="hidden" x-ref="file">
            <x-element.button @click="$refs.file.click()" class="bg-blue-500 text-white px-3 py-1 rounded">📎</x-element.button>
            <x-element.button id="send-button" class="bg-green-500 text-white px-3 py-1 rounded">ارسال</x-element.button>
        </div>

        <div id="user-box" class="mt-2 text-sm text-gray-500 flex flex-col gap-1"></div>
    </div>
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
            fileInputId: '#file-input',
        });
    </script>
    @endpush
@endif
