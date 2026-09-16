<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Property;
use App\Notifications\PropertyAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Notifies users when newly listed or updated properties match one of their
 * saved alerts.
 *
 * Runs daily from routes/console.php. It was previously written against a
 * schema that does not exist - it called $user->propertyAlerts (no such
 * relationship), read alert_types / price_change_threshold / min_bathrooms
 * (no such columns on `alerts`), and handed PropertyAlert plain arrays when
 * that notification reads Property models. It threw BadMethodCallException on
 * every run; nobody saw it because no scheduler was running on the server.
 * This rewrite matches the real `alerts` table.
 */
class CheckPropertyAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $recentProperties = Property::query()
            ->where(function ($query) {
                $query->where('created_at', '>=', now()->subDay())
                    ->orWhere('updated_at', '>=', now()->subDay());
            })
            ->get();

        if ($recentProperties->isEmpty()) {
            return;
        }

        // Group by user so someone with three overlapping alerts gets one
        // digest rather than three near-identical emails.
        $matchesByUser = [];

        Alert::query()
            ->where('is_active', true)
            ->with('user')
            ->chunk(100, function (Collection $alerts) use ($recentProperties, &$matchesByUser) {
                foreach ($alerts as $alert) {
                    if (! $alert->user) {
                        continue;
                    }

                    foreach ($this->matchingProperties($alert, $recentProperties) as $property) {
                        $matchesByUser[$alert->user_id]['user'] = $alert->user;
                        $matchesByUser[$alert->user_id]['properties'][$property->id] = $property;
                    }
                }
            });

        foreach ($matchesByUser as $match) {
            $match['user']->notify(new PropertyAlert(array_values($match['properties'])));
        }
    }

    /**
     * Properties satisfying every criterion the alert actually specifies.
     *
     * Each column on `alerts` is nullable, and a null criterion means "no
     * constraint" - not "match nothing". Treating null as a constraint would
     * silently stop every alert from ever firing.
     */
    private function matchingProperties(Alert $alert, Collection $properties): Collection
    {
        return $properties->filter(function (Property $property) use ($alert) {
            if ($alert->min_price !== null && (float) $property->price < (float) $alert->min_price) {
                return false;
            }

            if ($alert->max_price !== null && (float) $property->price > (float) $alert->max_price) {
                return false;
            }

            if ($alert->min_bedrooms !== null && (int) $property->bedrooms < (int) $alert->min_bedrooms) {
                return false;
            }

            if ($alert->max_bedrooms !== null && (int) $property->bedrooms > (int) $alert->max_bedrooms) {
                return false;
            }

            if (filled($alert->property_type) && $property->property_type !== $alert->property_type) {
                return false;
            }

            // Location is free text on both sides (a city, a district, a
            // building name), so an exact string comparison would almost
            // never match. Substring, case-insensitive, is the useful rule.
            if (filled($alert->location)) {
                $needle = mb_strtolower(trim($alert->location));
                $haystack = mb_strtolower((string) $property->location);

                if ($needle !== '' && ! str_contains($haystack, $needle)) {
                    return false;
                }
            }

            return true;
        })->values();
    }
}
