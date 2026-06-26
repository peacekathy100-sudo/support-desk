<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Company name <span class="text-danger">*</span></label>
        <input name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $client->company_name ?? '') }}" required>
        @error('company_name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Full name <span class="text-danger">*</span></label>
        <input name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $client->full_name ?? '') }}" required>
        @error('full_name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $client->email ?? '') }}" required>
        @error('email')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone ?? '') }}">
        @error('phone')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Username <span class="text-danger">*</span></label>
        <input name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $client->username ?? '') }}" required>
        @error('username')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <input name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $client->category ?? 'Standard') }}">
        @error('category')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Assigned representative <span class="text-danger">*</span></label>
        <select name="assigned_to_user_id" class="form-select @error('assigned_to_user_id') is-invalid @enderror" required>
            <option value="">Select user</option>
            @foreach($representatives as $rep)
            <option value="{{ $rep->user_id }}" @selected(old('assigned_to_user_id', $client->assigned_to_user_id ?? '') == $rep->user_id)>
                {{ $rep->user_name }} {{ $rep->user_surname }}
            </option>
            @endforeach
        </select>
        @error('assigned_to_user_id')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['active','inactive','suspended'] as $status)
            <option value="{{ $status }}" @selected(old('status', $client->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $client->notes ?? '') }}</textarea>
        @error('notes')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
