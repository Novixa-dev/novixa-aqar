<?php

namespace App\Support;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Rate limits for public forms that store a record and notify the office.
 *
 * Two budgets, both over 10 minutes: a tight one per IP + email (stops one
 * sender flooding the inbox) and a looser one per IP alone (stops email
 * rotation), so many real visitors behind one carrier-NAT address -- common
 * on Yemeni mobile networks -- don't share a 5-message budget.
 */
final class PublicFormThrottle
{
    public const PER_SENDER = 3;

    public const PER_IP = 20;

    private const DECAY_SECONDS = 600;

    /** Records the attempt and returns false once either budget is spent. */
    public static function attempt(string $form, ?string $ip, ?string $email): bool
    {
        $ipKey = "{$form}:ip:{$ip}";
        $senderKey = "{$form}:sender:{$ip}:".mb_strtolower(trim((string) $email));

        if (RateLimiter::tooManyAttempts($senderKey, self::PER_SENDER)
            || RateLimiter::tooManyAttempts($ipKey, self::PER_IP)) {
            return false;
        }

        RateLimiter::hit($senderKey, self::DECAY_SECONDS);
        RateLimiter::hit($ipKey, self::DECAY_SECONDS);

        return true;
    }
}
