<p>Hello {{ $client->full_name }},</p>
<p>Your client portal account for {{ $client->company_name }} has been created.</p>
<p><strong>Username:</strong> {{ $client->username }}<br>
<strong>Password:</strong> {{ $password }}</p>
<p>Sign in at <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>.</p>
<p>Your representative: {{ $representative }}</p>
