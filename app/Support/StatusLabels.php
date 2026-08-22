<?php

namespace App\Support;

use Illuminate\Support\Str;

class StatusLabels
{
    /**
     * A Filament formatStateUsing closure that renders a DB status value
     * through the localized lang/{locale}/statuses.php dictionary, falling
     * back to a headline-cased raw value for unknown states.
     */
    public static function for(string $domain): \Closure
    {
        return fn ($state) => self::label($domain, $state);
    }

    public static function label(string $domain, $state): string
    {
        $key = "statuses.{$domain}.{$state}";

        if (trans()->has($key)) {
            return __($key);
        }

        return Str::headline((string) $state);
    }

    /**
     * Badge colors per state; unknown states fall back to gray.
     */
    public static function color(string $domain): \Closure
    {
        return function ($state) use ($domain) {
            $map = match ($domain) {
                'lease' => ['active' => 'success', 'expired' => 'warning', 'terminated' => 'danger'],
                'payment' => ['completed' => 'success', 'paid' => 'success', 'pending' => 'warning', 'failed' => 'danger', 'void' => 'gray', 'refunded' => 'info'],
                'property' => ['For Rent' => 'info', 'For Sale' => 'success', 'pending' => 'warning', 'available' => 'success', 'rented' => 'info', 'sold' => 'gray'],
                'rental_application', 'moderation' => ['approved' => 'success', 'rejected' => 'danger', 'pending' => 'warning'],
                'booking', 'maintenance' => ['confirmed' => 'success', 'completed' => 'success', 'in_progress' => 'info', 'pending' => 'warning', 'cancelled' => 'danger'],
                'priority' => ['low' => 'gray', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'],
                'lead' => ['new' => 'info', 'contacted' => 'warning', 'qualified' => 'success', 'converted' => 'success', 'lost' => 'danger'],
                'campaign' => ['draft' => 'gray', 'scheduled' => 'info', 'sent' => 'success', 'failed' => 'danger'],
                default => [],
            };

            return $map[$state] ?? 'gray';
        };
    }
}
