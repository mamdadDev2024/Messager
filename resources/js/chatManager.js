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
        setTimeout(() => this.scrollToBottom(), 50);
        this._initListeners();

    }

    _initListeners() {
        this.channel.listen('.MessageSent', (e) => {
            if (e.user.id != this.user.id) {
                console.log(e)
                this._handleReceiveMessage(e)
            }
        });
        this.channel.listen('.ImageProcessed', e => this._handleImageBlur(e));
        this.channel.listenForWhisper('typing', e => this._handleTyping(e));

        this.channel
            .here(users => this._setOnlineUsers(users))
            .joining(user => this._updateUserStatus(user.id , 'آنلاین'))
            .leaving(user => this._updateUserStatus(user.id , 'آفلاین'));

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
        if (this.messageBox.querySelector(`[data-message-id="${message.id}"]`)) return;
        const isMe = message.user.id === this.user.id;
        const wrapper = document.createElement('div');
        wrapper.id = message.id;
        wrapper.className = `max-w-2xl w-fit p-4 rounded-2xl shadow transition-all duration-300 transform animate-fade-in
            ${ isMe ? 'ml-auto bg-green-50' : 'mr-auto bg-blue-50' }`;

        let fileHtml = '';
        if (message.attachment) {
            const fileUrl = `${this.baseUrl}/${message.attachment.url}`;
            if (message.attachment.type.startsWith('image/')) {
                fileHtml = `
                    <img src="${fileUrl}"
                        class="rounded-xl mb-3 max-h-60 w-full object-contain cursor-pointer hover:scale-105 transition-transform duration-200 {{ !$message->attachment->visible ? 'blur-sm' : '' }}"
                        title="برای نمایش کلیک کنید"
                        data-file-id="${message.attachment.id}" />
                `;
            } else {
                fileHtml = `
                    <a href="${fileUrl}"
                        class="inline-block text-blue-600 underline mb-3 truncate"
                        target="_blank">${message.attachment.file_name}</a>
                `;
            }
        }

        wrapper.innerHTML = `
            <div class="flex items-center justify-between mb-2 gap-2 text-xs text-gray-500">
                <span class="font-medium text-gray-700">${ message.user.username }</span>
                <div class="text-[10px] text-gray-400 mt-1 text-end">${this._formatTime(message.created_at)}</div>
            </div>
            ${fileHtml}
            <div>${message.text || ''}</div>
        `;
        wrapper.setAttribute('data-message-id', message.id);
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
        console.log(e);

        this._updateUserStatus({ id: e.id }, 'درحال نوشتن...');
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

        if (statusEl.textContent !== status) {
            statusEl.textContent = status;
        }

        clearTimeout(el.typingTimeout);

        if (status === 'درحال نوشتن...') {
            el.typingTimeout = setTimeout(() => {
                if (statusEl.textContent === 'درحال نوشتن...') {
                    statusEl.textContent = 'آنلاین';
                }
            }, 3000);
        }
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
