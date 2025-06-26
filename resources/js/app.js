import './bootstrap';

class ChannelManager {
    constructor({ id, user, messageBoxId, inputId, buttonId, userBoxId, fileInputId }) {
        this.chatId = id;
        this.user = user;
        this.messageBox = document.querySelector(messageBoxId);
        this.input = document.querySelector(inputId);
        this.button = document.querySelector(buttonId);
        this.userBox = document.querySelector(userBoxId);
        this.fileInput = document.querySelector(fileInputId);

        if (!this.messageBox || !this.input || !this.button || !this.userBox || !this.fileInput) {
            console.error("برخی از المنت‌های DOM پیدا نشدند.");
            return;
        }

        this.channel = window.Echo.join('chat.' + this.chatId);

        this._initListeners();
    }

    _initListeners() {
        this.channel.listen('.MessageSent', e => this._handleReceiveMessage(e));
        this.channel.listenForWhisper('typing', e => this._handleTyping(e));

        this.channel
            .here(users => this._setOnlineUsers(users))
            .joining(user => this._addUser(user))
            .leaving(user => this._removeUser(user));

        this.input.addEventListener('input', () => this._sendTyping());
        this.button.addEventListener('click', () => this._handleSend());
    }

    _handleSend() {
        const text = this.input.value.trim();
        const file = this.fileInput.files[0];

        if (!text && !file) return;

        if (file && file.size > 10 * 1024 * 1024) {
            alert("فایل نباید بیشتر از ۱۰ مگابایت باشد.");
            return;
        }

        const formData = new FormData();
        formData.append('chat_id', this.chatId);
        formData.append('text', text);
        if (file) formData.append('file', file);

        this.button.disabled = true;
        window.axios.post(`/chat/${this.chatId}/message`, formData)
            .then(() => {
                this.input.value = '';
                this.fileInput.value = '';
            })
            .catch(err => console.error('ارسال پیام با خطا مواجه شد:', err))
            .finally(() => {
                this.button.disabled = false;
            });
    }

    _handleReceiveMessage(e) {
        const message = e.message ?? e;
        if (!message?.user) return console.error('پیام نامعتبر:', message);

        const isMe = message.user.id === this.user.id;

        const el = document.createElement('div');
        el.className = `flex flex-col p-2 my-2 rounded shadow text-sm w-fit max-w-xs ${isMe ? 'bg-green-100 self-end' : 'bg-blue-100 self-start'}`;

        el.innerHTML = `
            <div class="font-bold text-gray-700">${message.user.name}</div>
            <div>${message.text || ''}</div>
            ${message.file ? `<a href="${message.file.url}" target="_blank" class="text-blue-500 underline">دانلود فایل</a>` : ''}
            <div class="text-gray-400 text-xs text-end">${message.created_at}</div>
        `;

        this.messageBox.appendChild(el);
        this.messageBox.scrollTop = this.messageBox.scrollHeight;
    }

    _sendTyping() {
        this.channel.whisper('typing', { id: this.user.id });
    }

    _handleTyping(e) {
        const el = this.userBox.querySelector(`[data-user-id="${e.id}"]`);
        if (!el) return;

        const status = el.querySelector('[data-status]');
        status.textContent = 'در حال نوشتن...';
        clearTimeout(el.typingTimeout);
        el.typingTimeout = setTimeout(() => {
            status.textContent = 'آنلاین';
        }, 3000);
    }

    _setOnlineUsers(users) {
        this.userBox.innerHTML = '';
        users.forEach(user => this._addUser(user));
    }

    _addUser(user) {
        if (this.userBox.querySelector(`[data-user-id="${user.id}"]`)) return;

        const el = document.createElement('div');
        el.className = 'flex items-center gap-2 p-1';
        el.setAttribute('data-user-id', user.id);

        el.innerHTML = `
            <img src="${user.avatar}" class="w-6 h-6 rounded-full" />
            <div>
                <div class="font-medium text-sm">${user.username}</div>
                <div class="text-xs text-green-500" data-status>آنلاین</div>
            </div>
        `;

        this.userBox.appendChild(el);
    }

    _removeUser(user) {
        const el = this.userBox.querySelector(`[data-user-id="${user.id}"]`);
        if (el) el.remove();
    }
}

window.ChannelManager = ChannelManager;
