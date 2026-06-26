<p>Hello {{ $client->full_name }},</p>
<p>Your client portal account has been suspended.</p>
@if($reason)
<p>Reason: {{ $reason }}</p>
@endif
@if($representative)
<p>Contact your representative if you have questions.</p>
@endif
