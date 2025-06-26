import './bootstrap';

class ChannelManager {
    constructor({ id, user, messageBoxId, inputId, buttonId, userBoxId, fileInputId, csrf, baseUrl }) {
        this.chatId = id;
        this.user = user;
        this.baseUrl = baseUrl;
        this.csrf = csrf;

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

        this.messageBox.scrollTop = this.messageBox.scrollHeight;

        this._initListeners();
    }

    _initListeners() {
        this.channel.listen('.ImageProcessed', e => this._handleImageBlur(e));

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
        const file = this.fileInput.files.length > 0 ? this.fileInput.files[0] : null;

        if (!text && !file) return;

        if (file && file.size > 10 * 1024 * 1024) {
            alert("حداکثر حجم فایل ۱۰ مگابایت است.");
            return;
        }

        const formData = new FormData();
        formData.append('chat_id', this.chatId);
        formData.append('text', text);
        if (file) formData.append('file', file);

        this.button.disabled = true;
        this.button.classList.add('opacity-50', 'cursor-not-allowed');

        window.axios.post(`/chat/${this.chatId}/message`, formData, {
            headers: { 'X-CSRF-TOKEN': this.csrf }
        })
        .then(response => {
            this.input.value = '';
            this.fileInput.value = '';
            const message = response.data.message;

            this._handleReceiveMessage({ message: { ...message, user: this.user } });
        })
        .catch(err => {
            alert('ارسال پیام با خطا مواجه شد. لطفاً دوباره تلاش کنید.');
            console.error('خطا در ارسال پیام:', err);
        })
        .finally(() => {
            this.button.disabled = false;
            this.button.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    }

    _handleReceiveMessage(e) {
        const message = e.message ?? e;

        if (!message?.user) {
            return console.error('پیام نامعتبر دریافت شد:', message);
        }

        const isMe = message.user.id === this.user.id;

        const el = document.createElement('div');
        el.className = `flex flex-col p-2 my-2 rounded shadow text-sm w-fit max-w-xs ${
            isMe ? 'bg-green-100 self-end' : 'bg-blue-100 self-start'
        }`;
        el.id = message.id;

        let fileHtml = '';

        if (message.attachment) {
            if (message.attachment.type.startsWith('image/')) {
                fileHtml = `
                    <div class="relative group mt-1">
                        <img
                            src="${this.baseUrl}/${message.attachment.url}"
                            data-file-id="${message.attachment.id}"
                            class="max-w-full rounded-lg transition duration-300"
                        />
                    </div>
                `;
            } else {
                fileHtml = `
                    <div class="mt-1">
                        <a href="${this.baseUrl}/${message.attachment.url}" target="_blank" class="text-blue-600 underline">
                            دانلود فایل: ${message.attachment.file_name}
                        </a>
                    </div>
                `;
            }
        }

        el.innerHTML = `
            <div class="font-bold text-gray-700">${message.user.username}</div>
            <div>${message.text || ''}</div>
            ${fileHtml}
            <div class="text-gray-400 text-xs text-end">${message.created_at ?? ''}</div>
        `;

        this.messageBox.appendChild(el);
        this.messageBox.scrollTop = this.messageBox.scrollHeight;
    }

    _handleImageBlur(e) {
        const fileId = e.file_id ?? (e.message?.attachment?.id ?? null);
        const visible = e.visible ?? (e.message?.attachment?.visible ?? 1);

        if (!fileId) return;

        const img = this.messageBox.querySelector(`img[data-file-id="${fileId}"]`);
        if (!img) return;

        if (visible === 0 || visible < 0.6) {
            img.classList.add('blur-md');
            img.title = 'تصویر به دلیل محتوای نامناسب تار شده است.';
        } else {
            img.classList.remove('blur-md');
            img.title = '';
        }
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
