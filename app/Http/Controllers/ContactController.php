<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        // Each submission is stored and emailed to the office inbox, so cap it
        // per IP like the other public forms -- and say so in the visitor's
        // language rather than showing a bare 429 page.
        $throttleKey = 'contact-form:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withInput()->withErrors([
                'message' => __('You have submitted too many requests recently. Please try again later.'),
            ]);
        }
        RateLimiter::hit($throttleKey, 600); // 5 per 10 minutes per IP

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'interest' => 'nullable|in:buying,selling,renting,other',
            'message' => 'required|string|max:5000',
        ]);

        $contactMessage = ContactMessage::create($validated);

        try {
            Mail::to(config('services.contact_inbox'))
                ->send(new ContactMessageReceived($contactMessage));
        } catch (\Throwable $e) {
            // The message is safely stored in the database (visible in the
            // admin inbox); a mail-transport outage must not lose it or break
            // the visitor's flow.
            Log::error('Failed to send contact message notification', [
                'contact_message_id' => $contactMessage->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', __('Your message has been sent successfully!'));
    }
}