@extends('layouts.client')

@section('content')
@php use App\Models\ExternalClient; @endphp

<div style="display: flex; height: calc(100vh - 120px); gap: 0; background: #fff;">
    <!-- Left Sidebar - Conversations List -->
    <div style="
        width: 380px;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        background: #fff;
    ">
        <!-- Header -->
        <div style="padding: 1.5rem; border-bottom: 1px solid #e0e0e0;">
            <h2 style="margin: 0 0 1rem 0; font-size: 1.5rem; font-weight: 700; color: #000;">Messages</h2>
            <div style="position: relative;">
                <input 
                    type="text" 
                    id="search-input"
                    placeholder="Search conversations..." 
                    style="
                        width: 100%;
                        padding: 0.7rem 1rem;
                        border: 1px solid #e0e0e0;
                        border-radius: 24px;
                        font-size: 0.95rem;
                        transition: all 0.2s;
                    "
                    onfocus="this.style.borderColor='#3496D7'; this.style.boxShadow='0 0 0 3px rgba(52,150,215,0.1)'"
                    onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'"
                >
            </div>
        </div>

        <!-- Conversations List -->
        <div style="
            flex: 1;
            overflow-y: auto;
            padding: 0;
        ">
            @forelse($conversations as $conversation)
                @php
                    $otherParticipant = $conversation->participants->firstWhere('participantable_type', '!=', ExternalClient::class);
                    $displayName = $otherParticipant?->participantable->full_name ?? 'Support Team';
                    $lastMessage = $conversation->latestMessage;
                    $initials = strtoupper(substr($displayName, 0, 2));
                @endphp
                <a href="{{ route('client.chat.show', $conversation) }}" style="
                    display: flex;
                    align-items: center;
                    padding: 0.8rem 0.8rem;
                    border-bottom: 1px solid #f0f0f0;
                    text-decoration: none;
                    color: inherit;
                    transition: all 0.15s;
                    background: #fff;
                " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">
                    <!-- Avatar -->
                    <div style="
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, #3496D7, #2980BB);
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: 600;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                        margin-right: 0.8rem;
                    ">{{ $initials }}</div>

                    <!-- Content -->
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.3rem; gap: 0.5rem;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: #000; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $displayName }}
                            </div>
                            <div style="font-size: 0.85rem; color: #999; white-space: nowrap;">
                                {{ optional($lastMessage)->created_at?->format('H:i') ?? '—' }}
                            </div>
                        </div>
                        <div style="font-size: 0.9rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $lastMessage ? Str::limit($lastMessage->body, 50) : 'No messages yet' }}
                        </div>
                    </div>
                </a>
            @empty
                <div style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    color: #999;
                    padding: 2rem;
                    text-align: center;
                ">
                    <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                    <p style="margin: 0; font-size: 1rem; font-weight: 500;">No conversations yet</p>
                    <p style="margin: 0.5rem 0 0; font-size: 0.9rem; opacity: 0.7;">Create a ticket to start messaging with support</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Section - Empty State -->
    <div style="
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        color: #999;
    ">
        <div style="text-align: center;">
            <i class="fas fa-comments" style="font-size: 4rem; opacity: 0.2; display: block; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem; font-weight: 500; margin: 0;">Select a conversation to chat</p>
            <p style="margin: 0.5rem 0 0; font-size: 0.95rem;">Click on a message thread or create a new ticket</p>
        </div>
    </div>
</div>

<style>
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
    div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
        background: #b0b0b0;
    }
</style>
@endsection
