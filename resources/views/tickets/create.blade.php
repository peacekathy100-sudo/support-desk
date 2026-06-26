@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 gap-3">
        <div>
            <h1 class="h4 mb-1" style="color:var(--brand-blue);">Create Ticket</h1>
            <p class="text-muted mb-0">Submit a new support ticket to the team.</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">Back to Tickets</a>
    </div>

    <div class="card shadow-sm" style="border-radius:var(--card-radius);">
        <div class="card-body p-3">
            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Subject</label>
                        <input name="subject" value="{{ old('subject') }}" class="form-control" required maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">General</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
                    </div>
                    @if(!auth()->user()->isClientRep())
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-select">
                                <option value="">None</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date</label>
                            <input name="due_at" type="date" value="{{ old('due_at') }}" class="form-control">
                        </div>
                    @endif
                    @if(auth()->user()->isAgent())
                        <div class="col-md-12">
                            <label class="form-label">Assign Agents</label>
                            <select name="agent_ids[]" multiple class="form-select" size="4">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->user_id }}" {{ in_array($agent->user_id, old('agent_ids', [])) ? 'selected' : '' }}>{{ $agent->user_surname }} {{ $agent->user_othername }} ({{ $agent->role?->ur_name }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl or Cmd to select multiple.</div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <label class="form-label">Attachments</label>
                        <input name="attachments[]" type="file" class="form-control" multiple>
                        <div class="form-text">Allowed types: jpg, png, pdf, docx, xlsx, txt.</div>
                    </div>
                    @if(!auth()->user()->isClientRep())
                        <div class="col-12 d-flex flex-column flex-md-row align-items-center gap-3">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="chargeable" name="chargeable" value="1" {{ old('chargeable') ? 'checked' : '' }}>
                                <label class="form-check-label" for="chargeable">Chargeable</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="notify_client" name="notify_client" value="1" {{ old('notify_client') ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_client">Notify client</label>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 text-end">
                        <button type="submit" class="btn" style="background:var(--brand-blue); color:#fff;">Submit Ticket</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
