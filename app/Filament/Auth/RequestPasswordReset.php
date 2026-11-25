<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * The panels' "request password reset" page, but a mail delivery failure
 * shows a localized notification instead of a server error (P18-7). Shared
 * by all nine panels via ->passwordReset(RequestPasswordReset::class).
 */
class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function request(): void
    {
        try {
            parent::request();
        } catch (TransportExceptionInterface $e) {
            report($e);

            Notification::make()
                ->title(__('We could not send the reset email right now. Please try again later or contact us.'))
                ->danger()
                ->send();
        }
    }
}
