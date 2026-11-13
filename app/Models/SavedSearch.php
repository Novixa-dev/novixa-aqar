<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;

    /** Most a single user may keep, so alerts stay a digest, not spam. */
    public const MAX_PER_USER = 20;

    /**
     * The /properties filters a search can be saved with, in the snake_case
     * vocabulary Property::scopeMatchingSearchCriteria() reads.
     */
    public const CRITERIA_KEYS = ['search', 'status', 'property_type', 'min_price', 'max_price', 'min_bedrooms', 'min_bathrooms'];

    protected $fillable = ['user_id', 'name', 'criteria', 'alerts_enabled', 'last_notified_at'];

    protected $casts = [
        'criteria' => 'array',
        'alerts_enabled' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Keep only known keys with a meaningful value, in a stable order, so two
     * saves of the same filters compare equal.
     */
    public static function normalizeCriteria(array $criteria): array
    {
        $normalized = [];

        foreach (self::CRITERIA_KEYS as $key) {
            $value = $criteria[$key] ?? null;

            if (in_array($key, ['search', 'status', 'property_type'], true)) {
                $value = trim((string) $value);
                if ($value !== '') {
                    $normalized[$key] = $value;
                }
            } elseif (is_numeric($value) && (float) $value > 0) {
                $normalized[$key] = (int) $value;
            }
        }

        return $normalized;
    }

    public function matchingProperties(): Builder
    {
        return Property::query()->publiclyVisible()->matchingSearchCriteria($this->criteria ?? []);
    }

    /** The /properties URL that reproduces this search (PropertyList's query-string names). */
    public function url(): string
    {
        $c = $this->criteria ?? [];
        $query = array_filter([
            'search' => $c['search'] ?? null,
            'status' => $c['status'] ?? null,
            'property_type' => $c['property_type'] ?? null,
            'minPrice' => $c['min_price'] ?? null,
            'maxPrice' => $c['max_price'] ?? null,
            'minBedrooms' => $c['min_bedrooms'] ?? null,
            'minBathrooms' => $c['min_bathrooms'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        return route('property.list', $query);
    }

    /** Human-readable summary, e.g. "للإيجار · فيلا · المكلا · 3+ غرف". */
    public static function describe(array $criteria, ?string $locale = null): string
    {
        $ar = ($locale ?? app()->getLocale()) === 'ar';
        $parts = [];

        if (! empty($criteria['status'])) {
            $parts[] = match ($criteria['status']) {
                'for_rent' => $ar ? 'للإيجار' : 'For rent',
                'for_sale' => $ar ? 'للبيع' : 'For sale',
                default => $criteria['status'],
            };
        }
        if (! empty($criteria['property_type'])) {
            $parts[] = (new Property)->forceFill(['property_type' => $criteria['property_type']])->translated_type;
        }
        if (! empty($criteria['search'])) {
            $parts[] = '"'.$criteria['search'].'"';
        }
        if (! empty($criteria['min_bedrooms'])) {
            $parts[] = $criteria['min_bedrooms'].'+ '.($ar ? 'غرف' : 'bedrooms');
        }
        if (! empty($criteria['min_bathrooms'])) {
            $parts[] = $criteria['min_bathrooms'].'+ '.($ar ? 'حمامات' : 'bathrooms');
        }
        if (! empty($criteria['min_price']) || ! empty($criteria['max_price'])) {
            $min = number_format((int) ($criteria['min_price'] ?? 0));
            $max = ! empty($criteria['max_price']) ? number_format((int) $criteria['max_price']) : '∞';
            $parts[] = ($ar ? 'السعر ' : 'Price ').$min.' – '.$max;
        }

        return $parts === [] ? ($ar ? 'كل العقارات' : 'All properties') : implode(' · ', $parts);
    }

    public function summary(?string $locale = null): string
    {
        return self::describe($this->criteria ?? [], $locale);
    }
}
