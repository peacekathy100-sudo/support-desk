<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateClientRequest;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $clients = Client::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%")
                      ->orWhere('client_email', 'like', "%{$search}%")
                      ->orWhere('client_code', 'like', "%{$search}%")
                      ->orWhere('client_contact', 'like', "%{$search}%")
                      ->orWhere('client_representative', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        abort(403, 'CRM clients are auto-synced from portal. Use ExternalClient to create portal users.');
    }

    public function store(Request $request): RedirectResponse
    {
        abort(403, 'CRM clients are auto-synced from portal. Use ExternalClient to create portal users.');
    }

    public function edit(Client $client): View
    {
        $this->gate('edit_clients');
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->gate('edit_clients');
        
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $client->update($data);

        return redirect()->route('clients.index')
                         ->with('success', "Client '{$client->client_name}' updated successfully.");
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->gate('delete_clients');

        DB::transaction(function () use ($client) {
            $client->tickets()->forceDelete();
            $client->forceDelete();
        });

        return redirect()->route('clients.index')
                         ->with('success', "Client {$client->client_name} and all associated tickets deleted.");
    }
}
