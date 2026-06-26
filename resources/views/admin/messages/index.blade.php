@extends('layouts.master')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div style="display: flex; height: calc(100vh - 100px);">
    <!-- Left: Conversation List -->
    <div style="width: 350px; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; background: #fff;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #e0e0e0;">
            <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700;">Messages</h2>
        </div>

        <div style="flex: 1; overflow-y: auto;">
            @forelse($conversations as $client)
                @php
                    $lastMsg = $client->messages->first();
                    $initials = strtoupper(substr($client->full_name ?? 'C', 0, 2));
                @endphp
                <a href="{{ route('admin.messages.show', $client->id) }}" style="
                    display: flex;
                    align-items: center;
                    padding: 1rem;
                    border-bottom: 1px solid #f0f0f0;
                    text-decoration: none;
                    color: inherit;
                    background: #fff;
                    border-left: 3px solid transparent;
                    transition: all 0.2s;
                " onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='#fff'">
                    <div style="
                        width: 48px;
                        height: 48px;
                        border-radius: 50%;
                        background: #3496D7;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: 600;
                        flex-shrink: 0;
                        margin-right: 1rem;
                    ">{{ $initials }}</div>
                    
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.95rem;">
                            {{ $client->full_name ?? 'Client' }}
                        </div>
                        <div style="font-size: 0.85rem; color: #999; margin-top: 0.3rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $lastMsg ? Str::limit($lastMsg->message, 40) : 'No messages' }}
                        </div>
                    </div>
                </a>
            @empty
                <div style="padding: 2rem 1rem; text-align: center; color: #999;">
                    <p>No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right: Welcome -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: #999;">
        <div style="text-align: center;">
            <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.2; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.1rem;">Select a conversation to start messaging</p>
        </div>
    </div>
</div>

@endsection
