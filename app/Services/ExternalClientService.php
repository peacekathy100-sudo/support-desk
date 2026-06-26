<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\ExternalClient;
use App\Models\SysUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Business logic for portal users (ExternalClient model).
 * CRM company records use the separate Client model.
 */
class ExternalClientService
{
    public function create(array $data): ExternalClient
    {
        $plainPassword = $data['password'] ?? Str::random(12);
        $data['password'] = Hash::make($plainPassword);
        $data['created_by'] = auth()->user()->user_id ?? null;

        $externalClient = ExternalClient::create($data);

        $this->logActivity('Portal client created', $externalClient);
        $this->sendWelcomeEmail($externalClient, $plainPassword);

        if ($externalClient->assignedRepresentative) {
            $this->notifyRepresentative($externalClient);
        }

        return $externalClient;
    }

    public function update(ExternalClient $externalClient, array $data): ExternalClient
    {
        $oldData = $externalClient->toArray();

        $externalClient->update($data);

        $this->logActivity('Portal client updated', $externalClient, $oldData, $data);

        return $externalClient;
    }

    public function resetPassword(ExternalClient $externalClient): string
    {
        $newPassword = Str::random(12);
        $externalClient->update(['password' => Hash::make($newPassword)]);

        $this->logActivity('Portal client password reset', $externalClient);
        $this->sendPasswordResetEmail($externalClient, $newPassword);

        return $newPassword;
    }

    public function suspend(ExternalClient $externalClient, string $reason = ''): void
    {
        $externalClient->update(['status' => 'suspended']);

        $this->logActivity('Portal client suspended: ' . $reason, $externalClient);
        $this->sendSuspensionEmail($externalClient, $reason);
    }

    public function activate(ExternalClient $externalClient): void
    {
        $externalClient->update(['status' => 'active']);

        $this->logActivity('Portal client activated', $externalClient);
        $this->sendActivationEmail($externalClient);
    }

    public function reassignRepresentative(
        ExternalClient $externalClient,
        SysUser $newRepresentative,
        string $reason = ''
    ): void {
        $oldRepresentative = $externalClient->assignedRepresentative;

        $externalClient->update(['assigned_to_user_id' => $newRepresentative->user_id]);

        $this->logActivity(
            'Portal client reassigned from ' . $oldRepresentative?->user_name . ' to ' . $newRepresentative->user_name . ': ' . $reason,
            $externalClient
        );

        $this->notifyRepresentative($externalClient);
        $this->sendReassignmentEmail($externalClient, $newRepresentative);
    }

    public function getByRepresentative(SysUser $user): Collection
    {
        return ExternalClient::where('assigned_to_user_id', $user->user_id)
            ->where('status', 'active')
            ->latest('last_activity_at')
            ->get();
    }

    public function search(string $query, int $limit = 10)
    {
        return ExternalClient::where(function ($q) use ($query) {
            $q->where('full_name', 'like', "%{$query}%")
                ->orWhere('company_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%");
        })
            ->where('status', 'active')
            ->limit($limit)
            ->get();
    }

    public function statistics(): array
    {
        return [
            'total' => ExternalClient::count(),
            'active' => ExternalClient::where('status', 'active')->count(),
            'inactive' => ExternalClient::where('status', 'inactive')->count(),
            'suspended' => ExternalClient::where('status', 'suspended')->count(),
        ];
    }

    protected function sendWelcomeEmail(ExternalClient $externalClient, ?string $plainPassword = null): void
    {
        try {
            Mail::send('emails.client.welcome', [
                'client' => $externalClient,
                'password' => $plainPassword ?? '(auto-generated)',
                'loginUrl' => route('client.login'),
                'representative' => $externalClient->assignedRepresentative?->user_name ?? 'Support Team',
            ], function ($message) use ($externalClient) {
                $message->to($externalClient->email)
                    ->subject('Welcome to ' . config('app.name') . ' - Your Client Portal Account');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to portal client: ' . $e->getMessage());
        }
    }

    protected function sendPasswordResetEmail(ExternalClient $externalClient, string $newPassword): void
    {
        try {
            Mail::send('emails.client.password-reset', [
                'client' => $externalClient,
                'password' => $newPassword,
                'loginUrl' => route('client.login'),
            ], function ($message) use ($externalClient) {
                $message->to($externalClient->email)
                    ->subject('Your Password Has Been Reset');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }

    protected function sendSuspensionEmail(ExternalClient $externalClient, string $reason = ''): void
    {
        try {
            Mail::send('emails.client.suspended', [
                'client' => $externalClient,
                'reason' => $reason,
                'representative' => $externalClient->assignedRepresentative,
            ], function ($message) use ($externalClient) {
                $message->to($externalClient->email)
                    ->subject('Account Suspended');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send suspension email: ' . $e->getMessage());
        }
    }

    protected function sendActivationEmail(ExternalClient $externalClient): void
    {
        try {
            Mail::send('emails.client.activated', [
                'client' => $externalClient,
                'loginUrl' => route('client.login'),
            ], function ($message) use ($externalClient) {
                $message->to($externalClient->email)
                    ->subject('Your Account Has Been Activated');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send activation email: ' . $e->getMessage());
        }
    }

    protected function sendReassignmentEmail(ExternalClient $externalClient, SysUser $newRepresentative): void
    {
        try {
            Mail::send('emails.client.reassigned', [
                'client' => $externalClient,
                'representative' => $newRepresentative,
                'representativePhone' => $newRepresentative->user_telephone,
                'representativeEmail' => $newRepresentative->user_email,
            ], function ($message) use ($externalClient) {
                $message->to($externalClient->email)
                    ->subject('Your Account Representative Has Changed');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send reassignment email: ' . $e->getMessage());
        }
    }

    protected function notifyRepresentative(ExternalClient $externalClient): void
    {
        // Representative in-app notifications can be wired when a dedicated notification class exists.
    }

    protected function logActivity(
        string $activity,
        ExternalClient $externalClient,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        try {
            AuditTrail::create([
                'user_id' => auth()->user()->user_id ?? null,
                'action' => $activity,
                'model' => 'ExternalClient',
                'model_id' => $externalClient->id,
                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
