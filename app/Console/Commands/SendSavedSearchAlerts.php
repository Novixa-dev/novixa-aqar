<?php

namespace App\Console\Commands;

use App\Models\SavedSearch;
use App\Notifications\SavedSearchMatches;
use Illuminate\Console\Command;

/**
 * Emails each user the listings published since their last alert that match
 * one of their saved searches (P33-2). Scheduled daily in routes/console.php.
 */
class SendSavedSearchAlerts extends Command
{
    protected $signature = 'saved-searches:send-alerts';

    protected $description = 'Notify users of new public listings matching their saved searches';

    public function handle(): int
    {
        $sent = 0;

        SavedSearch::query()
            ->where('alerts_enabled', true)
            ->with('user')
            ->chunkById(100, function ($searches) use (&$sent) {
                foreach ($searches as $search) {
                    if (! $search->user) {
                        continue;
                    }

                    $since = $search->last_notified_at ?? $search->created_at;
                    $checkedAt = now();

                    $matches = $search->matchingProperties()
                        ->where('created_at', '>', $since)
                        ->latest()
                        ->limit(10)
                        ->get();

                    if ($matches->isNotEmpty()) {
                        $search->user->notify(new SavedSearchMatches($search, $matches));
                        $sent++;
                    }

                    $search->forceFill(['last_notified_at' => $checkedAt])->save();
                }
            });

        $this->info("Sent {$sent} saved-search alert(s).");

        return self::SUCCESS;
    }
}
