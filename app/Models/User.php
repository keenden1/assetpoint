<?php

namespace App\Models;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

#[Fillable(['name', 'email', 'password', 'is_admin', 'is_super_admin', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Send the email verification notification.
     *
     * Sent synchronously. If the mail transport (e.g. Gmail SMTP) fails, we
     * swallow the exception so registration/resend still succeeds, log the
     * failure, and flash a flag the "verify email" view can surface so the
     * user knows to retry via the resend button.
     */
    public function sendEmailVerificationNotification(): void
    {
        try {
            $this->notify(new VerifyEmail);
            session()->forget('verification_email_failed');
        } catch (\Throwable $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            // Persist (not flash): registration redirects twice
            // (register -> /home -> /email/verify), which would consume a flash
            // before the verify page renders. Cleared once shown or on next
            // successful send.
            session()->put('verification_email_failed', true);
        }
    }
}
