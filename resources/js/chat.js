import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Simple debounce function to avoid lodash dependency
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const pusherKey = process.env.MIX_PUSHER_APP_KEY || process.env.PUSHER_APP_KEY || '';
const pusherCluster = process.env.MIX_PUSHER_APP_CLUSTER || 'mt1';

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster,
        wsHost: window.location.hostname,
        wsPort: process.env.MIX_PUSHER_PORT || 6001,
        wssPort: process.env.MIX_PUSHER_PORT || 6001,
        forceTLS: true,
        encrypted: true,
        disableStats: true,
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
    });
} else {
    console.warn('Pusher key is not configured. Live chat listeners are disabled.');
}

window.escapeHtml = function (value) {
    return String(value || '').replace(/[&<>"]+/g, function (chr) {
        const entities = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
        };
        return entities[chr] || chr;
    });
};

window.formatChatTimestamp = function (value) {
    try {
        return new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (error) {
        return value;
    }
};

window.chatMessageHtml = function (message, config) {
    const isMine = String(message.sender_id) === String(config.currentUserId)
        && message.sender_type === config.senderType;

    const bubbleClass = isMine ? 'bg-primary text-white' : 'bg-light text-dark';
    const alignClass = isMine ? 'justify-content-end' : 'justify-content-start';
    const senderLabel = isMine ? 'You' : (config.otherName || 'Support');

    return `
        <div class="d-flex ${alignClass} mb-3">
            <div class="card ${bubbleClass} shadow-sm border-0" style="max-width: 75%;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">${window.escapeHtml(senderLabel)}</div>
                    <div>${window.escapeHtml(message.body)}</div>
                    <div class="small text-end text-white-50 mt-2">${window.escapeHtml(window.formatChatTimestamp(message.created_at))}</div>
                </div>
            </div>
        </div>
    `;
};

window.scrollChatToBottom = function () {
    const container = document.querySelector('#chat-messages');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
};

window.updateTypingIndicator = function (event, config) {
    const indicator = document.querySelector('#typing-indicator');
    if (!indicator) {
        return;
    }

    if (String(event.sender_type) === String(config.senderType)) {
        return;
    }

    indicator.textContent = `${window.escapeHtml(event.user_name)} is typing...`;
    indicator.classList.remove('d-none');

    if (window.chatTypingClearTimeout) {
        clearTimeout(window.chatTypingClearTimeout);
    }

    window.chatTypingClearTimeout = setTimeout(() => {
        indicator.classList.add('d-none');
    }, 1500);
};

window.initChat = function (config) {
    if (!config) {
        return;
    }

    window.scrollChatToBottom();

    const channelName = `conversation.${config.conversationId}`;
    const textarea = document.querySelector('#chat-input');

    if (textarea && config.typingUrl) {
        textarea.addEventListener('input', debounce(() => {
            window.axios.post(config.typingUrl).catch(() => {
                // ignore typing indicator failures
            });
        }, 500));
    }

    if (!window.Echo) {
        return;
    }

    const channel = window.Echo.private(channelName);

    channel.listen('ChatMessageSent', (event) => {
        window.addIncomingChatMessage(event, config);
    });

    channel.listen('TypingIndicator', (event) => {
        window.updateTypingIndicator(event, config);
    });
};

window.addIncomingChatMessage = function (message, config) {
    const container = document.querySelector('#chat-messages');
    if (!container) {
        return;
    }

    const html = window.chatMessageHtml(message, config);
    container.insertAdjacentHTML('beforeend', html);
    window.scrollChatToBottom();
};

window.setChatConfig = function (config) {
    window.chatConfig = config;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.initChat(config));
    } else {
        window.initChat(config);
    }
};
