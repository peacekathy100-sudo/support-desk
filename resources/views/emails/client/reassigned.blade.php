<p>Hello {{ $client->full_name }},</p>
<p>Your account representative has changed.</p>
<p><strong>New representative:</strong> {{ $representative->user_name }} {{ $representative->user_surname }}</p>
@if($representativeEmail)
<p>Email: {{ $representativeEmail }}</p>
@endif
@if($representativePhone)
<p>Phone: {{ $representativePhone }}</p>
@endif
