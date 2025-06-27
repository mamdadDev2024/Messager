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

        if (![this.messageBox, this.input, this.button, this.userBox, this.fileInput].every(Boolean)) {
            console.error('❌ ChannelManager init error: missing DOM elements');
            return;
        }

        this.channel = window.Echo.join(`chat.${this.chatId}`);

        this.scrollToBottom();
        this._initListeners();

    }

    _initListeners() {
        this.channel.listen('MessageSent', (e) => {
            if (e.user.id != this.user.id) {
                this._handleReceiveMessage(e)
            }
        });
        this.channel.listen('ImageProcessed', e => this._handleImageBlur(e));
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
        const file = this.fileInput.files[0] || null;

        if (!text && !file) return;

        if (file && file.size > 10 * 1024 * 1024) {
            alert('حداکثر حجم فایل ۱۰ مگابایت است.');
            return;
        }

        const formData = new FormData();
        formData.append('chat_id', this.chatId);
        formData.append('text', text);
        if (file) formData.append('file', file);

        this._toggleButton(false);

        axios.post(`${this.chatId}/message`, formData, {
            headers: { 'X-CSRF-TOKEN': this.csrf }
        }).then(({ data }) => {
            this.input.value = '';
            this.fileInput.value = '';
            this._handleReceiveMessage({ message: { ...data.message, user: this.user } });
            window.dispatchEvent(new CustomEvent('sent'));
        }).catch(err => {
            alert('خطا در ارسال پیام');
            console.error(err);
        }).finally(() => this._toggleButton(true));
    }

    _handleReceiveMessage(e) {
        const message = e.message || e;
        if (!message.user) return;
        const isMe = message.user.id === this.user.id;
        const wrapper = document.createElement('div');
        wrapper.id = message.id;
        wrapper.className = `flex flex-col p-3 my-2 rounded-lg shadow text-sm max-w-xs w-fit ${
            isMe ? 'bg-green-100 self-end justify-end' : 'bg-blue-100 self-start justify-start'
        }`;

        let fileHtml = '';
        if (message.attachment) {
            const fileUrl = `${this.baseUrl}/${message.attachment.url}`;
            if (message.attachment.type.startsWith('image/')) {
                fileHtml = `
                    <img src="${fileUrl}"
                        data-file-id="${message.attachment.id}"
                        class="rounded mb-1 max-h-48 cursor-pointer"
                        title="برای نمایش کلیک کنید"/>
                `;
            } else {
                fileHtml = `
                    <a href="${fileUrl}"
                       class="text-blue-200 underline block mb-1 truncate"
                       target="_blank">${message.attachment.file_name}</a>
                `;
            }
        }

        wrapper.innerHTML = `
            <div class="font-bold text-gray-700">${message.user.username}</div>
            ${fileHtml}
            <div>${message.text || ''}</div>
            <div class="text-[10px] text-gray-400 mt-1 text-end">${this._formatTime(message.created_at)}</div>
        `;

        this.messageBox.appendChild(wrapper);
        this.scrollToBottom();
    }

    _handleImageBlur(e) {
        const fileId = e.file_id || e.message?.attachment?.id;
        const visible = e.attachment?.visible || e.message.attachment.visible;
        const img = this.messageBox.querySelector(`img[data-file-id="${fileId}"]`);
        if (!img) return;

        if (!visible) {
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
        this._updateUserStatus(e, 'درحال نوشتن...');
    }

    _setOnlineUsers(users) {
        this.userBox.innerHTML = '';
        users.forEach(u => this._addOrUpdateUser(u, 'آنلاین'));
    }

    _addUser(user) {
        this._addOrUpdateUser(user, 'آنلاین');
    }

    _removeUser(user) {
        const el = this.userBox.querySelector(`[data-user-id="${user.id}"]`);
        if (el) el.remove();
    }

    _addOrUpdateUser(user, status) {
        let el = this.userBox.querySelector(`[data-user-id="${user.id}"]`);
        if (el) el.remove();

        el = document.createElement('li');
        el.setAttribute('data-user-id', user.id);
        el.className = 'flex items-center gap-2 p-1';
        el.innerHTML = `
            <img src="${user.avatar?.url || '/default-avatar.png'}" class="w-6 h-6 rounded-full" />
            <div class="flex flex-col">
                <div class="font-medium text-sm">${user.username}</div>
                <div class="text-xs text-gray-500" data-status>${status}</div>
            </div>
        `;
        this.userBox.appendChild(el);
    }

    _updateUserStatus({ id }, status = 'درحال نوشتن...') {
        const el = this.userBox.querySelector(`[data-user-id="${id}"]`);
        if (!el) return;

        const statusEl = el.querySelector('[data-status]');
        if (!statusEl) return;

        statusEl.textContent = status;

        clearTimeout(el.typingTimeout);
        el.typingTimeout = setTimeout(() => {
            statusEl.textContent = 'آنلاین';
        }, 3000);
    }

    _toggleButton(enabled) {
        this.button.disabled = !enabled;
        this.button.classList.toggle('opacity-50', !enabled);
        this.button.classList.toggle('cursor-not-allowed', !enabled);
    }

    _formatTime(datetime) {
        if (!datetime) return '';
        const date = new Date(datetime);
        return date.toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
    }

    scrollToBottom() {
        this.messageBox.scrollTop = this.messageBox.scrollHeight;
    }
}

window.ChannelManager = ChannelManager;
