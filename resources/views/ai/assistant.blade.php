@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1" style="font-size:1.05rem; color:var(--brand-blue);">AI Assistant</h2>
            <div class="text-muted" style="font-size:0.9rem;">Ask for ticket triage suggestions, assignment ideas, or workflow guidance.</div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn" style="background:var(--brand-blue); color:#fff;">Back to Dashboard</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <form id="aiAssistantForm" class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.9rem;">Ask the assistant</label>
                        <textarea id="assistantPrompt" name="prompt" rows="5" class="form-control" placeholder="Example: What tickets should be escalated today?" required></textarea>
                        <button type="submit" class="btn mt-3" style="background:var(--brand-blue); color:#fff;">Get Suggestions</button>
                    </form>

                    <div id="assistantOutput" class="border rounded p-3" style="min-height:200px; background:#f8faff;">
                        <div class="text-muted">Enter a question above to get contextual guidance from the AI assistant.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm" style="border-radius:var(--card-radius);">
                <div class="card-body p-3">
                    <h5 class="mb-3" style="font-size:0.95rem; color:var(--brand-blue);">How this works</h5>
                    <ul class="ps-3" style="font-size:0.9rem;">
                        <li>Ask about ticket priorities, overdue work, or assignment strategy.</li>
                        <li>The assistant uses current ticket counts and status data.</li>
                        <li>It responds instantly without leaving the application.</li>
                    </ul>
                    <div class="mt-3 px-3 py-2 rounded" style="background:rgba(29,105,220,0.05);">
                        <strong>Tip:</strong> try asking "What should I do with overdue tickets?" or "Which client tickets need review?"
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('aiAssistantForm')?.addEventListener('submit', async function (event) {
        event.preventDefault();
        const output = document.getElementById('assistantOutput');
        const prompt = document.getElementById('assistantPrompt').value.trim();
        if (!prompt) return;

        output.innerHTML = '<div class="text-muted">Thinking... Please wait.</div>';

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('{{ route('ai.assist.suggest') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ prompt })
            });

            const data = await response.json();
            if (!response.ok || !data.ok) {
                output.innerHTML = '<div class="text-danger">Unable to fetch suggestions. Please try again later.</div>';
                return;
            }

            let html = '<div class="fw-semibold mb-2">Suggestions for:</div>';
            html += '<div class="fst-italic mb-3">"' + (data.prompt || prompt) + '"</div>';
            html += '<ul class="ps-3" style="font-size:0.95rem;">';
            data.suggestions.forEach(item => {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
            output.innerHTML = html;
        } catch (error) {
            output.innerHTML = '<div class="text-danger">An error occurred while contacting the assistant.</div>';
        }
    });
</script>
@endsection
