<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalClient;
use App\Models\SysUser;
use App\Services\ExternalClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Admin management for portal users (ExternalClient model).
 */
class ExternalClientController extends Controller
{
    public function __construct(private ExternalClientService $externalClientService)
    {
    }

    public function index(): View
    {
        $clients = ExternalClient::with(['assignedRepresentative', 'createdBy'])
            ->latest()
            ->paginate(15);

        return view('admin.external-clients.index', ['clients' => $clients]);
    }

    public function create(): View
    {
        $representatives = SysUser::where('user_status', 'active')->orderBy('user_surname')->get();

        return view('admin.external-clients.create', ['representatives' => $representatives]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:external_clients,email',
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|unique:external_clients,username|min:3|max:50',
            'category' => 'nullable|string|max:50',
            'assigned_to_user_id' => 'required|exists:sysuser,user_id',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        // Password is auto-generated, no need to validate it
        $externalClient = $this->externalClientService->create($validated);

        return redirect()
            ->route('admin.external-clients.show', $externalClient)
            ->with('success', 'Portal client account created. A password has been generated and sent by email.');
    }

    public function show(ExternalClient $externalClient): View
    {
        $externalClient->load(['assignedRepresentative', 'createdBy', 'tickets']);

        return view('admin.external-clients.show', ['client' => $externalClient]);
    }

    public function edit(ExternalClient $externalClient): View
    {
        $representatives = SysUser::where('user_status', 'active')->orderBy('user_surname')->get();

        return view('admin.external-clients.edit', [
            'client' => $externalClient,
            'representatives' => $representatives,
        ]);
    }

    public function update(Request $request, ExternalClient $externalClient): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:external_clients,email,' . $externalClient->id,
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|unique:external_clients,username,' . $externalClient->id . '|min:3|max:50',
            'category' => 'nullable|string|max:50',
            'assigned_to_user_id' => 'required|exists:sysuser,user_id',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        $this->externalClientService->update($externalClient, $validated);

        return redirect()
            ->route('admin.external-clients.show', $externalClient)
            ->with('success', 'Portal client updated successfully.');
    }

    public function destroy(ExternalClient $externalClient): RedirectResponse
    {
        $externalClient->delete();

        return redirect()
            ->route('admin.external-clients.index')
            ->with('success', 'Portal client deleted successfully.');
    }

    public function resetPassword(Request $request, ExternalClient $externalClient): RedirectResponse
    {
        if ($request->filled('password')) {
            $validated = $request->validate([
                'password' => 'required|min:6|confirmed',
            ]);

            $externalClient->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Portal client password has been reset.');
        }

        $this->externalClientService->resetPassword($externalClient);

        return back()->with('success', 'A new password has been generated and emailed when mail is configured.');
    }

    public function suspend(Request $request, ExternalClient $externalClient): RedirectResponse
    {
        $reason = $request->input('reason', '');
        $this->externalClientService->suspend($externalClient, $reason);

        return back()->with('success', 'Portal client account has been suspended.');
    }

    public function activate(ExternalClient $externalClient): RedirectResponse
    {
        $this->externalClientService->activate($externalClient);

        return back()->with('success', 'Portal client account has been activated.');
    }

    public function reassign(Request $request, ExternalClient $externalClient): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_to_user_id' => 'required|exists:sysuser,user_id',
            'reason' => 'nullable|string|max:500',
        ]);

        $representative = SysUser::findOrFail($validated['assigned_to_user_id']);

        $this->externalClientService->reassignRepresentative(
            $externalClient,
            $representative,
            $validated['reason'] ?? ''
        );

        return back()->with('success', 'Representative reassigned successfully.');
    }
}
