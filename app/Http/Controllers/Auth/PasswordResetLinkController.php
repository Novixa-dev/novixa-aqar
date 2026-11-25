<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController as FortifyPasswordResetLinkController;
use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Fortify's "send reset link" action, but a mail delivery failure becomes a
 * localized error on the form instead of a bare 500 page (P18-7). The
 * failure is still reported so it shows up in the logs.
 */
class PasswordResetLinkController extends Controller
{
    public function store(SendPasswordResetLinkRequest $request): Responsable|RedirectResponse
    {
        try {
            return app(FortifyPasswordResetLinkController::class)->store($request);
        } catch (TransportExceptionInterface $e) {
            report($e);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not send the reset email right now. Please try again later or contact us.')]);
        }
    }
}
