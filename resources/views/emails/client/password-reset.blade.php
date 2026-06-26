<p>Hello {{ $client->full_name }},</p>
<p>Your portal password has been reset.</p>
<p><strong>New password:</strong> {{ $password }}</p>
<p>Sign in at <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>.</p>
