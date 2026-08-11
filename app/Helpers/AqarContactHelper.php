<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;

class AqarContactHelper
{
    /**
     * Sanitize a phone number for international format (e.g. WhatsApp wa.me).
     * Strips non-digit characters, handles Yemen local prefixes (+967 / 07... / 7...),
     * and returns digit-only international number suitable for wa.me.
     */
    public static function sanitizeForWhatsApp(?string $phone = null): string
    {
        if (empty($phone)) {
            $phone = app(GeneralSettings::class)->site_phone ?? '+967770000000';
        }

        // Remove all non-digits
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return '967770000000';
        }

        // Handle Yemen local numbers (e.g., 0770000000 -> 967770000000, 770000000 -> 967770000000)
        if (str_starts_with($digits, '07') && strlen($digits) === 10) {
            $digits = '967' . substr($digits, 1);
        } elseif (str_starts_with($digits, '7') && strlen($digits) === 9) {
            $digits = '967' . $digits;
        }

        return $digits;
    }

    /**
     * Format phone number for tel: link click-to-call.
     */
    public static function formatForCall(?string $phone = null): string
    {
        if (empty($phone)) {
            $phone = app(GeneralSettings::class)->site_phone ?? '+967770000000';
        }

        // Retain + if present, strip spaces/dashes
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        return empty($cleaned) ? '+967770000000' : $cleaned;
    }

    /**
     * Generate pre-filled wa.me WhatsApp URL for a property inquiry.
     */
    public static function getWhatsAppUrl(?string $phone, string $title, int|string $id): string
    {
        $sanitizedPhone = self::sanitizeForWhatsApp($phone);
        $message = "السلام عليكم، أستفسر عن العقار: {$title} (رقم العقار: #{$id})";

        return "https://wa.me/{$sanitizedPhone}?text=" . rawurlencode($message);
    }

    /**
     * Generate social sharing URLs for a property listing.
     */
    public static function getShareUrls(string $url, string $title): array
    {
        $encodedUrl = rawurlencode($url);
        $encodedTitle = rawurlencode($title);
        $encodedCombined = rawurlencode("{$title} - {$url}");

        return [
            'whatsapp' => "https://api.whatsapp.com/send?text={$encodedCombined}",
            'twitter'  => "https://twitter.com/intent/tweet?text={$encodedTitle}&url={$encodedUrl}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'telegram' => "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}",
        ];
    }
}
