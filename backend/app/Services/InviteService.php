<?php

namespace App\Services;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class InviteService
{
    /**
     * Generate unique invite code
     */
    public function generateCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (Invite::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create invite
     */
    public function create(array $data): Invite
    {
        // Generate code if not provided
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode();
        }

        // Set default expires_at if not provided (7 days)
        if (empty($data['expires_at'])) {
            $data['expires_at'] = now()->addDays(7);
        }

        // Set default max_uses
        if (empty($data['max_uses'])) {
            $data['max_uses'] = 1;
        }

        return Invite::create($data);
    }

    /**
     * Send invite via email (placeholder)
     */
    public function sendEmail(Invite $invite): void
    {
        if (!$invite->target_email) {
            return;
        }

        $inviteUrl = config('app.frontend_url') . '/accept-invite/' . $invite->code;

        // TODO: Implement actual email sending
        if (app()->environment('local', 'development')) {
            Log::info("Email Invite", [
                'to' => $invite->target_email,
                'code' => $invite->code,
                'url' => $inviteUrl,
                'role' => $invite->target_role,
                'expires_at' => $invite->expires_at->toDateTimeString(),
            ]);
        }

        // Example implementation:
        // Mail::to($invite->target_email)->send(new InviteMail($invite, $inviteUrl));
    }

    /**
     * Send invite via SMS (placeholder)
     */
    public function sendSms(Invite $invite): void
    {
        if (!$invite->target_phone) {
            return;
        }

        $message = "Ban da duoc moi vao he thong. Ma moi: {$invite->code}. Ma co hieu luc den " . $invite->expires_at->format('d/m/Y H:i');

        if (app()->environment('local', 'development')) {
            Log::info("SMS Invite", [
                'phone' => $invite->target_phone,
                'code' => $invite->code,
                'message' => $message,
            ]);
            return;
        }

        // TODO: Implement SMS Gateway
        // Example: SMSGateway::send($invite->target_phone, $message);
    }

    /**
     * Validate invite code
     */
    public function validate(string $code, string $orgId): ?Invite
    {
        $invite = Invite::byCode($code)
            ->forOrg($orgId)
            ->first();

        if (!$invite) {
            return null;
        }

        // Check if expired
        if ($invite->isExpired() && $invite->status === 'PENDING') {
            $invite->markExpired();
            return null;
        }

        // Check if valid
        if (!$invite->canBeUsed()) {
            return null;
        }

        return $invite;
    }

    /**
     * Accept invite and create user
     */
    public function accept(Invite $invite, array $userData): User
    {
        if (!$invite->canBeUsed()) {
            throw new \Exception('Invite không còn hiệu lực');
        }

        // Create user
        $user = User::create([
            'org_id' => $invite->org_id,
            'role' => $invite->target_role,
            'full_name' => $userData['full_name'],
            'email' => $invite->target_email ?? $userData['email'] ?? null,
            'phone' => $invite->target_phone ?? $userData['phone'] ?? null,
            'password_hash' => $userData['password_hash'],
            'is_active' => true,
            'invited_by_user_id' => $invite->created_by_user_id,
        ]);

        // Assign role
        $roleName = match ($invite->target_role) {
            'MANAGER' => 'Manager',
            'STAFF' => 'Staff',
            'TENANT' => 'Tenant',
            default => 'Staff',
        };
        $user->syncRoles([$roleName]);

        // Update invite
        $invite->update([
            'accepted_by_user_id' => $user->id,
            'accepted_at' => now(),
        ]);
        $invite->incrementUse();

        // If property assigned, create property_user_role
        if ($invite->property_id && in_array($invite->target_role, ['MANAGER', 'STAFF'])) {
            \App\Models\PropertyUserRole::create([
                'org_id' => $invite->org_id,
                'property_id' => $invite->property_id,
                'user_id' => $user->id,
                'role' => $invite->target_role,
                'is_active' => true,
                'created_at' => now(),
            ]);
        }

        // TODO: If room assigned for TENANT, create room_tenant record

        return $user;
    }

    /**
     * Revoke invite
     */
    public function revoke(Invite $invite): void
    {
        $invite->revoke();
    }

    /**
     * Clean up expired invites
     */
    public function cleanupExpired(string $orgId): int
    {
        return Invite::forOrg($orgId)
            ->where('status', 'PENDING')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'EXPIRED',
            ]);
    }
}
