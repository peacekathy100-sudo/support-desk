@extends('layouts.client')

@section('content')

<div style="display: flex; height: calc(100vh - 120px); flex-direction: column; background: #fff;">
    <!-- Header -->
    <div style="
        background: linear-gradient(90deg, #3496D7, #2980BB);
        color: white;
        padding: 1.5rem;
    ">
        <h2 style="margin: 0; font-size: 1.3rem;">Support Team Messages</h2>
        <p style="margin: 0.3rem 0 0 0; opacity: 0.9;">Your conversation with our support team</p>
    </div>

    <!-- Messages -->
    <div style="
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    ">
        @forelse($messages as $msg)
            <div style="
                display: flex;
                justify-content: {{ $msg->sent_by === 'client' ? 'flex-end' : 'flex-start' }};
            ">
                <div style="
                    padding: 0.8rem 1rem;
                    border-radius: 16px;
                    background: {{ $msg->sent_by === 'client' ? '#28a745' : '#e5e5ea' }};
                    color: {{ $msg->sent_by === 'client' ? '#fff' : '#000' }};
                    max-width: 60%;
                    word-wrap: break-word;
                    animation: slideIn 0.3s ease-out;
                ">
                    <div>{{ $msg->message }}</div>
                    <div style="
                        font-size: 0.75rem;
                        margin-top: 0.4rem;
                        {{ $msg->sent_by === 'client' ? 'opacity: 0.8; color: rgba(255,255,255,0.8)' : 'opacity: 0.6' }}
                    ">
                        {{ $msg->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 2rem; color: #999;">
                <p>No messages yet. Start a conversation!</p>
            </div>
        @endforelse
    </div>

    <!-- Composer -->
    <div style="padding: 1.5rem; border-top: 1px solid #e0e0e0; background: #fff;">
        <form method="POST" action="{{ route('client.messages.send') }}">
            @csrf
            <div style="display: flex; gap: 0.8rem;">
                <textarea 
                    name="message"
                    placeholder="Type a message..."
                    style="
                        flex: 1;
                        padding: 0.8rem 1rem;
                        border: 1px solid #e0e0e0;
                        border-radius: 20px;
                        font-family: inherit;
                        font-size: 0.95rem;
                        resize: none;
                        min-height: 40px;
                        box-sizing: border-box;
                    "
                    onkeypress="if(event.key==='Enter' && !event.shiftKey) { this.form.submit(); event.preventDefault(); }"
                ></textarea>
                <button type="submit" style="
                    background: #28a745;
                    color: white;
                    border: 0;
                    padding: 0.8rem 1.2rem;
                    border-radius: 20px;
                    font-weight: 600;
                    cursor: pointer;
                    min-width: 50px;
                " onmouseover="this.style.background='#218838'" onmouseout="this.style.background='#28a745'">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            @if ($errors->any())
                <div style="color: #d32f2f; margin-top: 0.5rem; font-size: 0.9rem;">
                    @foreach ($errors->all() as $error)
                        <p style="margin: 0.2rem 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </form>
    </div>

    <!-- Red bottom line -->
    <div style="height: 3px; background: #d32f2f;"></div>
</div>

<style>
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
</style>

@endsection
