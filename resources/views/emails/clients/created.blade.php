@component('mail::message')
# New Client Created

A new client has been added to the support desk system.

@component('mail::panel')
**Client Details**

- **Name:** {{ $client->client_name }}
- **Email:** {{ $client->client_email ?? 'N/A' }}
- **Representative:** {{ $client->client_representative ?? 'N/A' }}
- **Contact:** {{ $client->client_contact ?? 'N/A' }}
- **Status:** {{ $client->is_active ? 'Active' : 'Inactive' }}
- **Created:** {{ $client->created_at->format('Y-m-d H:i') }}
@endcomponent

@component('mail::button', ['url' => route('clients.edit', $client)])
View Client Details
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
