@extends('layouts.client')

@section('content')
@php
    use App\Models\ExternalClient;
    $otherParticipant = $conversation->participants->firstWhere('participantable_type', '!=', ExternalClient::class);
    $otherName = $otherParticipant?->participantable->full_name ?? 'Support Team';
@endphp

<div style="display: flex; height: calc(100vh - 120px); gap: 0;">
    <!-- Left Sidebar - Conversations List -->
    <div style="
        width: 380px;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        background: #fff;
        overflow: hidden;
    ">
        <!-- Header -->
        <div style="padding: 1.5rem; border-bottom: 1px solid #e0e0e0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: #000;">Messages</h2>
                <a href="{{ route('client.chat.index') }}" style="
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: #f0f0f0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-decoration: none;
                    color: #3496D7;
                    font-size: 1.1rem;
                    transition: all 0.2s;
                " onmouseover="this.style.background='#3496D7'; this.style.color='white'" onmouseout="this.style.background='#f0f0f0'; this.style.color='#3496D7'">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <input 
                type="text" 
                placeholder="Search..." 
                style="
                    width: 100%;
                    padding: 0.7rem 1rem;
                    border: 1px solid #e0e0e0;
                    border-radius: 24px;
                    font-size: 0.95rem;
                "
                onfocus="this.style.borderColor='#3496D7'; this.style.boxShadow='0 0 0 3px rgba(52,150,215,0.1)'"
                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
            >
        </div>

        <!-- Conversations List (Mini) -->
        <div style="
            flex: 1;
            overflow-y: auto;
            padding: 0;
        ">
            @forelse($conversations as $conv)
                @php
                    $participant = $conv->participants->firstWhere('participantable_type', '!=', ExternalClient::class);
                    $name = $participant?->participantable->full_name ?? 'Support Team';
                    $isActive = $conv->id === $conversation->id;
                    $initials = strtoupper(substr($name, 0, 2));
                @endphp
                <a href="{{ route('client.chat.show', $conv) }}" style="
                    display: flex;
                    align-items: center;
                    padding: 0.8rem 0.8rem;
                    border-bottom: 1px solid #f0f0f0;
                    text-decoration: none;
                    color: inherit;
                    background: {{ $isActive ? '#e8f3ff' : '#fff' }};
                    border-left: 4px solid {{ $isActive ? '#3496D7' : 'transparent' }};
                ">
                    <div style="
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #3496D7, #2980BB);
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: 600;
                        font-size: 0.9rem;
                        flex-shrink: 0;
                        margin-right: 0.8rem;
                    ">{{ $initials }}</div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.9rem; color: #000; overflow: hidden; text-overflow: ellipsis;">{{ $name }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Right Section - Chat -->
    <div style="
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    ">
        <!-- Sticky Header -->
        <div style="
            background: linear-gradient(90deg, #3496D7, #2980BB);
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #2980BB;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        ">
            <div>
                <h3 style="margin: 0 0 0.3rem 0; font-size: 1.1rem; font-weight: 600;">{{ $otherName }}</h3>
                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">{{ $conversation->subject ?? 'Support Chat' }}</p>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="chat-messages" class="messages-container" style="
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        ">
            @forelse($conversation->messages as $message)
                @php $isMine = $message->sender_type === ExternalClient::class; @endphp
                <div style="
                    display: flex; 
                    justify-content: {{ $isMine ? 'flex-end' : 'flex-start' }}; 
                    margin-bottom: 0.5rem; 
                    align-items: flex-end; 
                    gap: 0.5rem;
                    animation: slideIn 0.3s ease-out;
                " class="message-row">
                    @if(!$isMine)
                        <img src="https://via.placeholder.com/32?text={{ substr($otherName, 0, 1) }}" style="
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            flex-shrink: 0;
                        " alt="Avatar">
                    @endif
                    <div style="
                        display: flex;
                        flex-direction: column;
                        align-items: {{ $isMine ? 'flex-end' : 'flex-start' }};
                        gap: 0.2rem;
                    ">
                        @if(!$isMine)
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0 0.5rem; color: #666;">{{ $otherName }}</span>
                        @endif
                        <div style="
                            max-width: 65%;
                            padding: 0.7rem 1rem;
                            border-radius: 18px;
                            background: {{ $isMine ? '#3496D7' : '#e5e5ea' }};
                            color: {{ $isMine ? 'white' : '#000' }};
                            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
                            word-wrap: break-word;
                            word-break: break-word;
                            line-height: 1.4;
                            position: relative;
                        " class="message-bubble" data-message-id="{{ $message->id }}">
                            <div style="font-size: 0.95rem; line-height: 1.5; margin-bottom: 0.3rem;">{{ $message->body }}</div>
                            <div style="
                                font-size: 0.75rem; 
                                margin-top: 0.3rem; 
                                display: flex;
                                align-items: center;
                                gap: 0.4rem;
                                {{ $isMine ? 'opacity: 0.7; justify-content: flex-end' : 'opacity: 0.7' }}
                            ">
                                <span>{{ $message->created_at->format('H:i') }}</span>
                                @if($isMine)
                                    <span>
                                        @if($message->read_at)
                                            <i class="fas fa-check-double" style="color: #fff; opacity: 1;" title="Read"></i>
                                        @elseif($message->delivered_at)
                                            <i class="fas fa-check-double" style="color: #fff; opacity: 0.7;" title="Delivered"></i>
                                        @else
                                            <i class="fas fa-check" style="color: #fff; opacity: 0.5;" title="Sent"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #999;">
                    <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                    <p style="margin: 0; font-size: 1rem;">No messages yet</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.9rem; opacity: 0.7;">Send a message to start the conversation</p>
                </div>
            @endforelse
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="small text-muted d-none" style="padding: 0.5rem 1.5rem; color: #999; font-size: 0.9rem; display: flex; gap: 0.5rem; align-items: center;">
            <span style="display: inline-flex; gap: 0.3rem;">
                <span style="display: inline-block; width: 6px; height: 6px; background: #999; border-radius: 50%; animation: bounce 1.4s infinite;"></span>
                <span style="display: inline-block; width: 6px; height: 6px; background: #999; border-radius: 50%; animation: bounce 1.4s infinite 0.2s;"></span>
                <span style="display: inline-block; width: 6px; height: 6px; background: #999; border-radius: 50%; animation: bounce 1.4s infinite 0.4s;"></span>
            </span>
            <span id="typing-name">{{ $otherName }} is typing</span>
        </div>

        <!-- Message Composer -->
        <div style="
            padding: 1rem 1.5rem;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 0.8rem;
            align-items: flex-end;
            background: #fff;
            flex-shrink: 0;
        ">
            <div style="flex: 1; display: flex; gap: 0.5rem; align-items: flex-end;">
                <button type="button" style="
                    background: transparent;
                    border: 0;
                    color: #3496D7;
                    font-size: 1.2rem;
                    cursor: pointer;
                    padding: 0;
                    transition: all 0.2s;
                " title="Attach file" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fas fa-paperclip"></i>
                </button>
                <textarea 
                    id="chat-input" 
                    rows="1" 
                    placeholder="Type a message..." 
                    style="
                        flex: 1;
                        border-radius: 20px;
                        padding: 0.7rem 1rem;
                        border: 1px solid #e0e0e0;
                        font-family: inherit;
                        font-size: 0.95rem;
                        resize: vertical;
                        max-height: 100px;
                        transition: all 0.2s;
                    "
                    onfocus="this.style.borderColor='#3496D7'; this.style.boxShadow='0 0 0 3px rgba(52,150,215,0.1)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
                    onkeypress="if(event.key==='Enter' && !event.shiftKey) { window.sendChatMessage(); }"
                ></textarea>
                <div style="position: relative;">
                    <button type="button" id="emoji-picker-btn" style="
                        background: transparent;
                        border: 0;
                        color: #3496D7;
                        font-size: 1.2rem;
                        cursor: pointer;
                        padding: 0;
                        transition: all 0.2s;
                    " title="Emoji" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fas fa-smile"></i>
                    </button>
                    <!-- Emoji Picker Popup -->
                    <div id="emoji-popup" style="
                        display: none;
                        position: absolute;
                        bottom: 45px;
                        right: 0;
                        background: white;
                        border: 1px solid #e0e0e0;
                        border-radius: 12px;
                        padding: 1rem;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        z-index: 1000;
                        min-width: 250px;
                    ">
                        <div style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.8rem; color: #333;">Quick Reactions</div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.8rem;">
                            <button type="button" class="emoji-quick" data-emoji="👍" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">👍</button>
                            <button type="button" class="emoji-quick" data-emoji="❤️" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">❤️</button>
                            <button type="button" class="emoji-quick" data-emoji="😂" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">😂</button>
                            <button type="button" class="emoji-quick" data-emoji="😮" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">😮</button>
                            <button type="button" class="emoji-quick" data-emoji="😢" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">😢</button>
                            <button type="button" class="emoji-quick" data-emoji="🎉" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">🎉</button>
                            <button type="button" class="emoji-quick" data-emoji="✅" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">✅</button>
                            <button type="button" class="emoji-quick" data-emoji="🚀" style="background: transparent; border: 0; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'; this.style.transform='scale(1.2)'" onmouseout="this.style.background='transparent'; this.style.transform='scale(1)'">🚀</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="chat-submit-btn" onclick="window.sendChatMessage()" style="
                background: #3496D7;
                color: white;
                border: 0;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                cursor: pointer;
                transition: all 0.2s;
                flex-shrink: 0;
            " onmouseover="this.style.background='#2980BB'; this.style.transform='scale(1.05)'" onmouseout="this.style.background='#3496D7'; this.style.transform='scale(1)'">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(52, 150, 215, 0.3) transparent;
}

.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background: rgba(52, 150, 215, 0.3);
    border-radius: 4px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: rgba(52, 150, 215, 0.5);
}

#chat-input {
    max-height: 120px;
}

div[style*="overflow-y: auto"] {
    scrollbar-width: thin;
    scrollbar-color: #d0d0d0 transparent;
}

div[style*="overflow-y: auto"]::-webkit-scrollbar {
    width: 6px;
}

div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
    background: transparent;
}

div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
    background: #d0d0d0;
    border-radius: 3px;
}

.message-bubble {
    transition: all 0.3s ease;
}

.message-bubble:hover {
    box-shadow: 0 2px 12px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.message-row {
    animation: slideIn 0.3s ease-out;
}

/* WhatsApp-style animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce {
    0%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-8px);
    }
}

@media (max-width: 768px) {
    div[style*="width: 380px"] {
        display: none !important;
    }
}
</style>

@push('scripts')
<script>
    // ================================================================
    // BULLETPROOF MESSAGE SENDER - Debug + Error Handling
    // ================================================================
    
    console.log('✅ Chat script loaded');
    
    window.sendChatMessage = function() {
        try {
            console.log('🔧 sendChatMessage called');
            
            // Get input element
            const chatInput = document.getElementById('chat-input');
            if (!chatInput) {
                console.error('❌ Chat input element NOT found');
                alert('Error: Chat input not found');
                return;
            }
            
            const messageText = chatInput.value.trim();
            console.log('📝 Message text:', messageText, '(length:', messageText.length + ')');
            
            if (!messageText) {
                console.log('⚠️  Message is empty, skipping');
                return;
            }
            
            // Get CSRF token
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) {
                console.error('❌ CSRF meta tag NOT found');
                alert('Security error: CSRF token not found');
                return;
            }
            const csrfToken = csrfMeta.content;
            console.log('🔐 CSRF token:', csrfToken.substring(0, 10) + '...');
            
            // Get conversation ID
            const conversationId = "{{ $conversation->id }}";
            console.log('🗂️  Conversation ID:', conversationId);
            
            if (!conversationId) {
                console.error('❌ Conversation ID is empty');
                alert('Error: Conversation not found');
                return;
            }
            
            // Build exact URL
            const url = `/client/chat/${conversationId}/message`;
            console.log('🌐 Full URL:', window.location.origin + url);
            
            // Create FormData
            const formData = new FormData();
            formData.append('body', messageText);
            console.log('📦 FormData created with field "body"');
            
            // Disable button
            const submitBtn = document.getElementById('chat-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                console.log('🔒 Submit button disabled');
            }
            
            // Send fetch
            console.log('⏳ Sending fetch request...');
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('📊 Response received:');
                console.log('  Status:', response.status, response.statusText);
                console.log('  Type:', response.type);
                console.log('  URL:', response.url);
                
                // Handle 302/301 redirects (success)
                if (response.status === 302 || response.status === 301) {
                    console.log('✅ Got redirect (success response)');
                    chatInput.value = '';
                    console.log('💾 Input cleared');
                    setTimeout(() => {
                        console.log('🔄 Reloading page...');
                        window.location.reload();
                    }, 300);
                    return;
                }
                
                // Handle 200/201 (success)
                if (response.status === 200 || response.status === 201) {
                    console.log('✅ Success response');
                    chatInput.value = '';
                    setTimeout(() => window.location.reload(), 300);
                    return;
                }
                
                // Handle errors
                console.log('❌ Error status:', response.status);
                return response.text().then(text => {
                    console.log('📄 Response body:', text);
                    
                    // Try to parse as JSON
                    try {
                        const json = JSON.parse(text);
                        console.log('📋 Parsed JSON:', json);
                        
                        if (json.errors) {
                            console.error('❌ Validation errors:', json.errors);
                            const errorMessages = [];
                            for (const field in json.errors) {
                                errorMessages.push(`${field}: ${json.errors[field].join(', ')}`);
                            }
                            alert('Validation Error:\n\n' + errorMessages.join('\n'));
                        } else if (json.message) {
                            console.error('❌ Error message:', json.message);
                            alert('Error: ' + json.message);
                        } else {
                            console.error('❌ Unknown error response');
                            alert('Error: ' + JSON.stringify(json));
                        }
                    } catch (parseError) {
                        console.error('❌ Could not parse JSON:', parseError);
                        console.error('❌ Raw text:', text.substring(0, 200));
                        alert('Error: ' + text.substring(0, 200));
                    }
                });
            })
            .catch(networkError => {
                console.error('❌ Network/Fetch error:', networkError);
                console.error('   Message:', networkError.message);
                console.error('   Stack:', networkError.stack);
                alert('Network error: ' + networkError.message);
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    console.log('🔓 Submit button enabled');
                }
            });
            
        } catch (error) {
            console.error('❌ Caught exception:', error);
            console.error('   Message:', error.message);
            console.error('   Stack:', error.stack);
            alert('Error: ' + error.message);
        }
    };

    // Emoji Picker Toggle
    const emojiPickerBtn = document.getElementById('emoji-picker-btn');
    const emojiPopup = document.getElementById('emoji-popup');
    const chatInput = document.getElementById('chat-input');

    emojiPickerBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        emojiPopup.style.display = emojiPopup.style.display === 'none' ? 'block' : 'none';
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', (e) => {
        if (e.target !== emojiPickerBtn && e.target.closest('#emoji-popup') === null) {
            emojiPopup.style.display = 'none';
        }
    });

    // Emoji Quick Reactions
    document.querySelectorAll('.emoji-quick').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const emoji = btn.dataset.emoji;
            chatInput.value += emoji;
            emojiPopup.style.display = 'none';
            chatInput.focus();
        });
    });

    // Auto-scroll to bottom
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    window.chatTypingUrl = "{{ route('client.chat.typing', $conversation) }}";
    window.setChatConfig({
        conversationId: "{{ $conversation->id }}",
        currentUserId: "{{ $client->id }}",
        senderType: "{{ addslashes(ExternalClient::class) }}",
        typingUrl: window.chatTypingUrl,
        otherName: "{{ addslashes($otherName) }}",
    });
    
    console.log('✅ All chat handlers initialized');
</script>
@endpush
@endsection
