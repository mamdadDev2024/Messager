
import './bootstrap';

class ChannelManager {
  constructor(parameters = {}) {
    this.chatId = parameters.id;
    this.user = parameters.user;
    this.messageBox = document.querySelector(parameters.messageBoxId);
    this.input = document.querySelector(parameters.inputId);
    this.button = document.querySelector(parameters.buttonId);
    this.userBox = document.querySelector(parameters.userBoxId);
    this.channel = window.Echo.channel('chat.' + this.chatId);
    this.listener_init();
  }

  listener_init() {
    this.channel
      .listen('MessageSent', (e) => this.handleReceiveMessage(e.message))
      .listenForWhisper('typing', (e) => this.handleTyping(e))
      .here(this._handle_online_users.bind(this))
      .joining(this._handle_join_user.bind(this))
      .error(this._handle_error.bind(this));

    this.input.addEventListener('keydown', (e) => this.handleTyping(e));
    this.button.addEventListener('click', (e) => this.handleSendMessage(e));

    document.addEventListener('livewire:init', () => {
    });
  }

  _handle_join_user(user) {
    console.log('User joined:', user);
  }

  _handle_online_users(users) {
    console.log('Online users:', users);
  }

  handleTyping(event) {
    if (event.type === 'keydown' && event.key !== 'Enter') return;
    this.channel.whisper('typing', { id: this.user.id });
  }

  handleSendMessage(event) {
    const message = this.input.value;
    Livewire.emit('sendMessage', message, this.chatId);
    this.input.value = '';
  }

  handleReceiveMessage(message) {
    this._add_message_el_to_messagebox(message);
  }

  handleTyping(e) {
    this._add_user_to_userbox(e.id);
  }

  _add_message_el_to_messagebox(message) {
    const p = document.createElement('p');
    p.innerText = message.text;
    this.messageBox.appendChild(p);
  }

  _add_user_to_userbox(id) {
    const div = document.createElement('div');
    div.innerText = `User ${id} is typing...`;
    this.userBox.appendChild(div);
  }

  _handle_error(error) {
    console.error('Error:', error);
  }
}
