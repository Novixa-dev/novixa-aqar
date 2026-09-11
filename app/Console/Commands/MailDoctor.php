<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Proves the application's own mail path works.
 *
 * A provider can report a message as delivered while this application still
 * cannot send one: those are two different things, and only the second
 * matters. Resend accepting an API call says nothing about whether
 * MAIL_HOST, MAIL_USERNAME and MAIL_PASSWORD are right, whether the SMTP
 * port is reachable from the container, or whether the queue connection
 * lets a queued notification run at all.
 *
 * This sends one message through Laravel's configured mailer -- the same
 * path the contact form and every notification take -- and reports what
 * happened.
 */
class MailDoctor extends Command
{
    protected $signature = 'mail:doctor {--to= : Recipient (defaults to the contact inbox)}';

    protected $description = 'Send one message through the application mailer and report the result';

    public function handle(): int
    {
        $mailer = config('mail.default');
        $to = (string) ($this->option('to') ?: config('services.contact_inbox'));

        $this->line("MAIL| mailer:    {$mailer}");
        $this->line('MAIL| host:      '.(config("mail.mailers.{$mailer}.host") ?: '(n/a)'));
        $this->line('MAIL| port:      '.(config("mail.mailers.{$mailer}.port") ?: '(n/a)'));
        $this->line('MAIL| username:  '.(filled(config("mail.mailers.{$mailer}.username")) ? '(set)' : '(EMPTY)'));
        $this->line('MAIL| password:  '.(filled(config("mail.mailers.{$mailer}.password")) ? '(set)' : '(EMPTY)'));
        $this->line('MAIL| from:      '.config('mail.from.address'));
        $this->line('MAIL| queue:     '.config('queue.default'));
        $this->line("MAIL| to:        {$to}");

        if (blank($to)) {
            $this->line('MAIL| [FAIL] no recipient configured (CONTACT_INBOX_EMAIL / MAIL_FROM_ADDRESS are both empty)');

            return self::FAILURE;
        }

        if ($mailer === 'log' || $mailer === 'array') {
            $this->line("MAIL| [NOTE] mailer is [{$mailer}] -- nothing actually leaves the application.");

            return self::SUCCESS;
        }

        try {
            Mail::raw(
                "This is an automated check from Novixa Aqar.\n\n".
                "It was sent through the application's own configured mailer, not through a provider API, ".
                "so its arrival proves the contact form and every notification can reach a real inbox.\n\n".
                'Sent at '.now()->toDateTimeString()." UTC.\n",
                function ($message) use ($to) {
                    $message->to($to)->subject('Novixa Aqar — mail path check');
                },
            );
        } catch (Throwable $e) {
            $this->line('MAIL| [FAIL] '.class_basename($e).': '.$e->getMessage());

            return self::FAILURE;
        }

        $this->line('MAIL| [PASS] the mailer accepted the message without error');

        return self::SUCCESS;
    }
}
