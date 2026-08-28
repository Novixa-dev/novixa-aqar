<?php

namespace App\Models;

use App\Services\WalkScoreService;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
/**
 * Represents a property in the real estate application.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property float $price
 * @property int $bedrooms
 * @property int $bathrooms
 * @property float $area_sqft
 * @property int $year_built
 * @property string $property_type
 * @property string $status
 * @property DateTime $list_date
 * @property DateTime|null $sold_date
 * @property int $user_id
 * @property int $agent_id
 * @property string|null $virtual_tour_url
 * @property string|null $model_3d_url
 * @property bool $is_featured
 * @property string|null $rightmove_id
 * @property string|null $zoopla_id
 * @property string|null $onthemarket_id
 * @property DateTime|null $last_synced_at
 * @property DateTime|null $deleted_at
 * @property-read Collection|Appointment[] $appointments
 * @property-read Collection|Transaction[] $transactions
 * @property-read Collection|Review[] $reviews
 * @property-read Collection|PropertyFeature[] $features
 * @property-read Collection|Image[] $images
 * @property-read Collection|Booking[] $bookings
 */
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Property extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * Application-level default for any insert path that omits `country`
     * (this is a Yemen-first platform; the DB column's own legacy default
     * is still 'UK', inherited from the upstream template — fixed here
     * rather than via a schema ALTER, which would need doctrine/dbal).
     */
    protected $attributes = [
        'country' => 'Yemen',
    ];

    protected $fillable = [
        'title',
        'description',
        'internal_notes',
        'property_template_id',
        'location',
        'structured_address',
        'latitude',
        'longitude',
        'walkability_score',
        'walkability_description',
        'transit_score',
        'transit_description',
        'bike_score',
        'bike_description',
        'walkability_updated_at',
        'price',
        'currency',
        'bedrooms',
        'bathrooms',
        'reception_rooms',
        'parking',
        'gardens',
        'area_sqft',
        'year_built',
        'property_type',
        'status',
        'moderation_status',
        'moderated_at',
        'moderation_notes',
        'list_date',
        'sold_date',
        'user_id',
        'team_id',
        'branch_id',
        'agent_id',
        'virtual_tour_url',
        'virtual_tour_provider',
        'live_tour_available',
        'model_3d_url',
        'is_featured',
        'rightmove_id',
        'zoopla_id',
        'onthemarket_id',
        'last_synced_at',
        'neighborhood_id',
        'property_category_id',
        'postal_code',
        'country',
        'energy_rating',
        'epc',
        'energy_score',
        'energy_rating_date',
        'insurance_policy_id',
        'insurance_coverage_amount',
        'insurance_premium',
        'insurance_expiry_date',
        'floor_plan_data',
        'floor_plan_image',
        'jupix_id',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'list_date' => 'date',
        'sold_date' => 'date',
        'moderated_at' => 'datetime',
        'is_featured' => 'boolean',
        'live_tour_available' => 'boolean',
        'insurance_expiry_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'walkability_updated_at' => 'datetime',
        'floor_plan_data' => 'array',
        'structured_address' => 'array',
        'parking' => 'array',
        'gardens' => 'array',
        'epc' => 'array',
    ];

    public function insurancePolicy()
    {
        return $this->belongsTo(InsurancePolicy::class);
    }

    public function hasActiveInsurance()
    {
        return $this->insurance_policy_id && $this->insurance_expiry_date > now();
    }

    public function template()
    {
        return $this->belongsTo(PropertyTemplate::class, 'property_template_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('moderation_status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved');
    }

    /**
     * Canonical listing-status values for the seeder/Staff-form convention.
     * `status` also carries a second, separate, legitimate vocabulary used
     * by the V1 API/AutomationController (draft, available, under_offer,
     * sstc, exchanged, to_let, let, let_agreed, withdrawn, archived,
     * pending, approved, rejected, ...) — those are distinct real states,
     * not synonyms of these four, and must NOT be collapsed into them.
     * setStatusAttribute() below only rewrites the narrow, unambiguous
     * legacy Title-Case values (which have no other meaning anywhere in
     * the codebase) to their snake_case equivalent; everything else,
     * including the broader vocabulary above, passes through untouched.
     */
    public const STATUS_FOR_SALE = 'for_sale';
    public const STATUS_FOR_RENT = 'for_rent';
    public const STATUS_SOLD = 'sold';
    public const STATUS_RENTED = 'rented';

    /**
     * Only the exact legacy Title-Case strings PropertySeeder/
     * PropertyFactory historically wrote — each has no other meaning
     * anywhere else in the codebase, so remapping them is unambiguous.
     */
    private const STATUS_SYNONYMS = [
        'For Sale' => self::STATUS_FOR_SALE,
        'For Rent' => self::STATUS_FOR_RENT,
        'Sold' => self::STATUS_SOLD,
        'Rented' => self::STATUS_RENTED,
    ];

    /**
     * Status values that mean "listed and visible to buyers/tenants".
     * Includes both this model's canonical values and the V1 API's
     * separate vocabulary's equivalent ('available'/'active') — a
     * read-side inclusive list, not a write-side normalization target.
     */
    public const ACTIVE_STATUSES = [self::STATUS_FOR_SALE, self::STATUS_FOR_RENT, 'active', 'available', 'For Sale', 'For Rent'];

    public static function canonicalStatus(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        return self::STATUS_SYNONYMS[$raw] ?? $raw;
    }

    protected function setStatusAttribute(?string $value): void
    {
        $this->attributes['status'] = self::canonicalStatus($value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    public function approve(): void
    {
        $this->update(['moderation_status' => 'approved', 'moderated_at' => now()]);
    }

    public function reject(): void
    {
        $this->update(['moderation_status' => 'rejected', 'moderated_at' => now()]);
    }

    public function setYearBuiltAttribute($value)
    {
        $this->attributes['year_built'] = is_string($value) ? substr($value, 0, 4) : $value;
    }

    // Relationships
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'property_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'property_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function features()
    {
        return $this->hasMany(PropertyFeature::class, 'property_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function viewCount()
    {
        return $this->activities()->where('type', 'property_view')->count();
    }

    public function similarProperties($limit = 3)
    {
        return Property::where('id', '!=', $this->id)
            ->where('property_type', $this->property_type)
            ->where('currency', $this->currency)
            ->whereBetween('price', [$this->price * 0.8, $this->price * 1.2])
            ->whereBetween('bedrooms', [$this->bedrooms - 1, $this->bedrooms + 1])
            ->whereBetween('bathrooms', [$this->bathrooms - 1, $this->bathrooms + 1])
            ->limit($limit)
            ->get();
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function communications()
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function category()
    {
        return $this->belongsTo(PropertyCategory::class, 'property_category_id');
    }

    public function valuations()
    {
        return $this->hasMany(PropertyValuation::class);
    }

    public function chainLinks()
    {
        return $this->hasMany(ChainLink::class);
    }

    public function complianceItems()
    {
        return $this->hasMany(ComplianceItem::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function vendorQuotes()
    {
        return $this->hasMany(VendorQuote::class);
    }

    public function propertyMatches()
    {
        return $this->hasMany(PropertyMatch::class);
    }

    public function marketAppraisals()
    {
        return $this->hasMany(MarketAppraisal::class);
    }

    public function histories()
    {
        return $this->hasMany(PropertyHistory::class)->orderBy('event_date', 'desc');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'property_id', 'user_id')
            ->withTimestamps();
    }

    public function communityEvents()
    {
        return $this->hasMany(CommunityEvent::class);
    }

    public function getNearbyCommunityEvents($radius = 10)
    {
        if (! $this->latitude || ! $this->longitude) {
            return collect([]);
        }

        // Use the nearby scope which includes distance calculation
        return CommunityEvent::public()
            ->upcoming()
            ->nearby($this->latitude, $this->longitude, $radius)
            ->get()
            ->map(function ($event) {
                // Distance is already calculated by the nearby scope
                // but we need to make it accessible as a property
                $event->distance_from_property = $event->distance ?? 0;

                return $event;
            });
    }

    public function getLatestValuation($type = 'market')
    {
        return $this->valuations()
            ->where('valuation_type', $type)
            ->where('status', 'active')
            ->latest('valuation_date')
            ->first();
    }

    public function getComplianceStatus()
    {
        $total = $this->complianceItems()->count();
        $completed = $this->complianceItems()->where('status', 'completed')->count();
        $overdue = $this->complianceItems()->where('required_by_date', '<', now())
            ->where('status', '!=', 'completed')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
        ];
    }

    public function hasActiveWorkOrders()
    {
        return $this->workOrders()
            ->whereIn('status', ['pending', 'approved', 'scheduled', 'in_progress'])
            ->exists();
    }

    public function getLatestMarketAppraisal()
    {
        return $this->marketAppraisals()
            ->where('valid_until', '>=', now())
            ->latest('appraisal_date')
            ->first();
    }

    /**
     * Update walkability scores for this property
     *
     * @return void
     */
    public function updateWalkabilityScores()
    {
        if (! $this->latitude || ! $this->longitude) {
            return;
        }

        $walkScoreService = app(WalkScoreService::class);
        $address = $this->location.', '.$this->postal_code;

        $scores = $walkScoreService->getWalkScore($address, $this->latitude, $this->longitude);

        if ($scores) {
            $this->update([
                'walkability_score' => $scores['walk_score'],
                'walkability_description' => $scores['walk_description'],
                'transit_score' => $scores['transit_score'],
                'transit_description' => $scores['transit_description'],
                'bike_score' => $scores['bike_score'],
                'bike_description' => $scores['bike_description'],
                'walkability_updated_at' => now(),
            ]);
        }
    }

    /**
     * Check if walkability scores need updating (older than 30 days)
     *
     * @return bool
     */
    public function needsWalkabilityUpdate()
    {
        if (! $this->walkability_updated_at) {
            return true;
        }

        return $this->walkability_updated_at->lt(now()->subDays(30));
    }

    // Scopes
    public function scopeSearch(Builder $query, $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('location', 'like', '%'.$search.'%')
                ->orWhere('postal_code', 'like', '%'.$search.'%');
        });
    }

    public function scopePostalCode(Builder $query, $postalCode): Builder
    {
        return $query->where('postal_code', 'like', $postalCode.'%');
    }

    public function scopeNearby(Builder $query, $latitude, $longitude, $radius): Builder
    {
        return $query->selectRaw('*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    public function scopeCategory(Builder $query, $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * A numeric price range only makes sense within one currency — comparing
     * raw numbers across YER/SAR/USD would be misleading — so this always
     * scopes to a single currency (the app default unless one is given).
     */
    public function scopePriceRange(Builder $query, $min, $max, ?string $currency = null): Builder
    {
        return $query
            ->where('currency', $currency ?? config('currencies.default'))
            ->whereBetween('price', [$min, $max]);
    }

    public function scopeBedrooms(Builder $query, $min, $max): Builder
    {
        return $query->whereBetween('bedrooms', [$min, $max]);
    }

    public function scopeBathrooms(Builder $query, $min, $max): Builder
    {
        return $query->whereBetween('bathrooms', [$min, $max]);
    }

    public function scopeAreaRange(Builder $query, $min, $max): Builder
    {
        return $query->whereBetween('area_sqft', [$min, $max]);
    }

    public function scopePropertyType(Builder $query, $type): Builder
    {
        return $query->where('property_type', $type);
    }

    public function scopeHasAmenities(Builder $query, array $amenities): Builder
    {
        return $query->whereHas('features', function ($query) use ($amenities) {
            $query->whereIn('feature_name', $amenities);
        }, '=', count($amenities));
    }

    public function scopeNeedsSyncing(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNull('last_synced_at')
                ->orWhereColumn('updated_at', '>', 'last_synced_at');
        });
    }

    public function scopeEnergyRating(Builder $query, $rating): Builder
    {
        return $query->where('energy_rating', $rating);
    }

    public function scopeMinEnergyScore(Builder $query, $minScore): Builder
    {
        return $query->where('energy_score', '>=', $minScore);
    }

    public function scopeWalkabilityScore(Builder $query, $minScore): Builder
    {
        return $query->where('walkability_score', '>=', $minScore);
    }

    public function scopeTransitScore(Builder $query, $minScore): Builder
    {
        return $query->where('transit_score', '>=', $minScore);
    }

    public function scopeBikeScore(Builder $query, $minScore): Builder
    {
        return $query->where('bike_score', '>=', $minScore);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeCountry(Builder $query, $country): Builder
    {
        return $query->where('country', $country);
    }

    public function getAvailableDatesForTeam()
    {
        $bookedDates = $this->bookings()
            ->where('team_id', $this->team_id)
            ->pluck('date')
            ->toArray();

        $teamBookings = Booking::where('team_id', $this->team_id)
            ->pluck('date')
            ->toArray();

        $availableDates = [];
        $startDate = now();
        $endDate = now()->addMonths(3);

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $currentDate = $date->format('Y-m-d');
            if (! in_array($currentDate, $bookedDates) && ! in_array($currentDate, $teamBookings)) {
                $availableDates[] = $currentDate;
            }
        }

        return $availableDates;
    }

    /**
     * Check if property has a virtual tour
     *
     * @return bool
     */
    public function hasVirtualTour()
    {
        return $this->generateEmbedCode($this->virtual_tour_url) !== null;
    }

    /**
     * Get the embedded virtual tour HTML
     *
     * @return string|null
     */
    public function getVirtualTourEmbed()
    {
        if ($this->virtual_tour_url) {
            return $this->generateEmbedCode($this->virtual_tour_url);
        }

        return null;
    }

    /**
     * Generate embed code from URL for common virtual tour providers
     *
     * @param  string  $url
     * @return string|null
     */
    protected function generateEmbedCode($url)
    {
        // Validate URL
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = ['matterport.com', 'kuula.co', '3dvista.com', '3dv.st', 'seekbeak.com'];
        $isAllowed = collect($allowedHosts)->contains(
            fn (string $allowed) => $host === $allowed || str_ends_with($host, '.'.$allowed)
        );

        if (! $isAllowed || strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https') {
            return null;
        }

        $escapedUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return '<iframe width="100%" height="480" src="'.$escapedUrl.'" frameborder="0" sandbox="allow-scripts allow-same-origin allow-presentation" referrerpolicy="no-referrer" allow="xr-spatial-tracking" allowfullscreen></iframe>';
    }

    public function registerMediaCollections(): void
    {
        // useDisk('public') is required explicitly: without it, media
        // uploaded through Filament's SpatieMediaLibraryFileUpload field
        // silently lands on `filesystems.default` (local — a private,
        // non-web-accessible disk) instead of the public one these
        // collections are actually served from, even though the config
        // default (media-library.disk_name) is 'public'. Verified live:
        // reproduced (an uploaded property photo 404'd forever), fixed.
        // Pre-existing seeded photos were already correct only because
        // PropertySeeder's addMedia() call happens to inherit the app
        // config's disk default at the time it runs, not because the
        // collection itself pinned one — this makes it explicit and
        // consistent regardless of upload path.
        $this->addMediaCollection('images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();

        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsMimeTypes(['video/mp4', 'video/quicktime', 'application/x-empty'])
            ->singleFile();

        $this->addMediaCollection('3d_models')
            ->useDisk('public')
            ->acceptsMimeTypes(['model/gltf-binary', 'model/gltf+json', 'application/octet-stream', 'application/x-empty'])
            ->singleFile();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($property) {
            Cache::flush();
        });

        static::updated(function ($property) {
            Cache::flush();
        });

        static::deleted(function ($property) {
            Cache::flush();
        });
    }

    public function isHmo(): bool
    {
        return ($this->property_type ?? '') === 'HMO';
    }

    public function getImageUrlAttribute(): string
    {
        $media = $this->getFirstMediaUrl('images');
        if (!empty($media)) {
            return $media;
        }

        $stockImages = [
            'Villa' => [
                'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            ],
            'Apartment' => [
                'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
            ],
            'Land' => [
                'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1628744448840-55bdb2497bd4?auto=format&fit=crop&w=1200&q=80',
            ],
            'Commercial' => [
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
            ],
            'Building' => [
                'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1555636222-cae831e670b3?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        $type = $this->property_type ?? 'Apartment';
        $images = $stockImages[$type] ?? $stockImages['Apartment'];
        $index = abs(((int)($this->id ?? 1)) % count($images));

        return $images[$index];
    }

    public function getTranslatedTypeAttribute(): string
    {
        $types = [
            'Apartment' => app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Apartment',
            'Villa' => app()->getLocale() === 'ar' ? 'فيلا فاخرة' : 'Luxury Villa',
            // The only property_type value actually stored lowercase
            // ('house', not 'House') — every other key here exactly matches
            // its real stored casing, so this one silently never matched
            // and fell through to the raw value below.
            'house' => app()->getLocale() === 'ar' ? 'منزل مستقل' : 'Independent House',
            'Land' => app()->getLocale() === 'ar' ? 'أرض سكنية / تجارية' : 'Land / Plot',
            'Commercial' => app()->getLocale() === 'ar' ? 'عقار تجاري / مكتب' : 'Commercial / Office',
            'Building' => app()->getLocale() === 'ar' ? 'عمارة استثمارية' : 'Investment Building',
            'HMO' => app()->getLocale() === 'ar' ? 'سكن مشترك' : 'Shared Living (HMO)',
        ];

        return $types[$this->property_type] ?? ($this->property_type ?: (app()->getLocale() === 'ar' ? 'عقار سكني' : 'Residential'));
    }

    /**
     * The listing price formatted with its own currency's symbol/decimals —
     * never assumes every property shares one global site currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return app(\App\Services\CurrencyService::class)->format($this->price, $this->currency);
    }
}
