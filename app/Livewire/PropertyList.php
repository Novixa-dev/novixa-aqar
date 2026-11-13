<?php

namespace App\Livewire;

use App\Models\Activity;
use Log;
use Exception;
use Livewire\Component;
use App\Models\Property;
use App\Models\Favorite;
use App\Models\PropertyFeature;
use App\Models\SavedSearch;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\PropertyFeatureService;

class PropertyList extends Component
{
    use WithPagination;

    public $search = '';
    public $minPrice = 0;
    public $maxPrice = 1000000000;
    public $minBedrooms = 0;
    public $maxBedrooms = 10;
    public $minBathrooms = 0;
    public $maxBathrooms = 10;
    public $minArea = 0;
    public $maxArea = 10000;
    public $propertyType = '';
    public $selectedAmenities = [];
    public $yearBuilt = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $postalCode = '';
    public $latitude = null;
    public $longitude = null;
    public $radius = 10; // Default radius in km
    public $energyRating = '';
    public $minEnergyScore = 0;
    public $minWalkabilityScore = 0;
    public $minTransitScore = 0;
    public $minBikeScore = 0;
    public $featuredOnly = false;
    public $country = '';

    protected $listeners = ['applyAdvancedFilters', 'favoriteAdded' => '$refresh', 'favoriteRemoved' => '$refresh'];

    public function mount()
    {
        $this->resetPage();
    }
    
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 1000000000],
        'minBedrooms' => ['except' => 0],
        'maxBedrooms' => ['except' => 10],
        'minBathrooms' => ['except' => 0],
        'maxBathrooms' => ['except' => 10],
        'minArea' => ['except' => 0],
        'maxArea' => ['except' => 10000],
        // Aliased: the homepage search form (resources/views/home.blade.php)
        // and every other property-type <select> across the public site use
        // the snake_case field name `property_type`, not the camelCase PHP
        // property name — without this alias Livewire's query-string binding
        // looks for `?propertyType=` instead, so the type filter picked on
        // the homepage silently never reaches this component.
        'propertyType' => ['as' => 'property_type', 'except' => ''],
        'selectedAmenities' => ['except' => []],
        'energyRating' => ['except' => ''],
        'minEnergyScore' => ['except' => 0],
        'minWalkabilityScore' => ['except' => 0],
        'minTransitScore' => ['except' => 0],
        'minBikeScore' => ['except' => 0],
        'featuredOnly' => ['except' => false],
        'country' => ['except' => ''],
    ];

    public function applyAdvancedFilters($filters)
    {
        $this->search = $filters['search'];
        $this->minPrice = $filters['minPrice'];
        $this->maxPrice = $filters['maxPrice'];
        $this->minBedrooms = $filters['minBedrooms'];
        $this->maxBedrooms = $filters['maxBedrooms'];
        $this->minBathrooms = $filters['minBathrooms'];
        $this->maxBathrooms = $filters['maxBathrooms'];
        $this->minArea = $filters['minArea'];
        $this->maxArea = $filters['maxArea'];
        $this->propertyType = $filters['propertyType'];
        $this->selectedAmenities = $filters['selectedAmenities'];

        $this->resetPage();
    }

    public function getPropertiesProperty()
    {
        try {
            $query = Property::query()->publiclyVisible()->matchingSearchCriteria($this->currentCriteria());

            if ($this->featuredOnly) {
                $query->where('is_featured', true);
            }

            $query->latest()->with('media');

            return $query->paginate(12);
        } catch (Exception $e) {
            return Property::publiclyVisible()->latest()->with('media')->paginate(12);
        }
    }

    /** This page's active filters in SavedSearch::CRITERIA_KEYS form. */
    public function currentCriteria(): array
    {
        return SavedSearch::normalizeCriteria([
            'search' => $this->search,
            'status' => $this->status,
            'property_type' => $this->propertyType,
            'min_price' => $this->minPrice,
            // 1,000,000,000 is this page's "no maximum" sentinel.
            'max_price' => (int) $this->maxPrice < 1000000000 ? $this->maxPrice : 0,
            'min_bedrooms' => $this->minBedrooms,
            'min_bathrooms' => $this->minBathrooms,
        ]);
    }

    /** "Save this search": remember the filters and alert on new matches. */
    public function saveSearch()
    {
        $user = Auth::user();
        $isArabic = app()->getLocale() === 'ar';

        if (! $user) {
            session()->put('url.intended', url()->previous());

            return $this->redirectRoute('login');
        }

        $criteria = $this->currentCriteria();

        if ($criteria === []) {
            session()->flash('saved_search_error', $isArabic ? 'اختر فلتراً واحداً على الأقل قبل حفظ البحث.' : 'Pick at least one filter before saving this search.');

            return;
        }

        $existing = $user->savedSearches()->get();

        if ($existing->contains(fn (SavedSearch $saved) => $saved->criteria == $criteria)) {
            session()->flash('saved_search_success', $isArabic ? 'هذا البحث محفوظ لديك مسبقاً.' : 'You have already saved this search.');

            return;
        }

        if ($existing->count() >= SavedSearch::MAX_PER_USER) {
            session()->flash('saved_search_error', $isArabic
                ? 'وصلت للحد الأقصى ('.SavedSearch::MAX_PER_USER.') من عمليات البحث المحفوظة. احذف بحثاً قديماً أولاً.'
                : 'You have reached the limit of '.SavedSearch::MAX_PER_USER.' saved searches. Delete an old one first.');

            return;
        }

        $user->savedSearches()->create([
            'name' => SavedSearch::describe($criteria),
            'criteria' => $criteria,
            'alerts_enabled' => true,
            // Alert only on listings published after saving, not the current results.
            'last_notified_at' => now(),
        ]);

        session()->flash('saved_search_success', $isArabic
            ? 'تم حفظ البحث. سنرسل لك تنبيهاً عند نشر عقارات جديدة مطابقة.'
            : 'Search saved. We will alert you when new matching listings are published.');
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->dispatch('filtersChanged', $this->getFilters());
    }

    public function getActiveFiltersCountProperty(): int
    {
        $count = 0;
        if (trim((string) $this->search) !== '') $count++;
        if ($this->status !== '') $count++;
        if ($this->propertyType !== '') $count++;
        if ((int) $this->minBedrooms > 0) $count++;
        if ((int) $this->minBathrooms > 0) $count++;
        if ((int) $this->minPrice > 0) $count++;
        if ((int) $this->maxPrice > 0 && (int) $this->maxPrice < 1000000000) $count++;

        return $count;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->propertyType = '';
        $this->minBedrooms = 0;
        $this->minBathrooms = 0;
        $this->minPrice = 0;
        $this->maxPrice = 1000000000;
        $this->resetPage();
    }

    public function applyFilters($filters)
    {
        $this->fill($filters);
        $this->resetPage();
    }

    private function getFilters()
    {
        return [
            'search' => $this->search,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'minBedrooms' => $this->minBedrooms,
            'maxBedrooms' => $this->maxBedrooms,
            'minBathrooms' => $this->minBathrooms,
            'maxBathrooms' => $this->maxBathrooms,
            'minArea' => $this->minArea,
            'maxArea' => $this->maxArea,
            'propertyType' => $this->propertyType,
            'selectedAmenities' => $this->selectedAmenities,
            'energyRating' => $this->energyRating,
            'minEnergyScore' => $this->minEnergyScore,
            'minWalkabilityScore' => $this->minWalkabilityScore,
            'minTransitScore' => $this->minTransitScore,
            'minBikeScore' => $this->minBikeScore,
            'featuredOnly' => $this->featuredOnly,
            'country' => $this->country,
        ];
    }

    private function getCacheKey()
    {
        return 'properties_' . md5(json_encode([
            $this->search,
            $this->minPrice,
            $this->maxPrice,
            $this->minBedrooms,
            $this->maxBedrooms,
            $this->minBathrooms,
            $this->maxBathrooms,
            $this->minArea,
            $this->maxArea,
            $this->propertyType,
            $this->selectedAmenities,
            $this->energyRating,
            $this->minEnergyScore,
            $this->minWalkabilityScore,
            $this->minTransitScore,
            $this->minBikeScore,
            $this->featuredOnly,
            $this->country,
        ]));
    }
    
    
    protected $propertyFeatureService;
    
    public function boot(PropertyFeatureService $propertyFeatureService)
    {
        $this->propertyFeatureService = $propertyFeatureService;
    }
    
    protected function seoTitleAndDescription(): array
    {
        $isArabic = app()->getLocale() === 'ar';

        $statusLabel = match ($this->status) {
            'for_sale' => $isArabic ? 'للبيع' : 'for sale',
            'for_rent' => $isArabic ? 'للإيجار' : 'for rent',
            default => null,
        };

        $parts = [];
        $parts[] = $statusLabel
            ? ($isArabic ? "عقارات {$statusLabel}" : "Properties {$statusLabel}")
            : ($isArabic ? 'العقارات المتاحة' : 'Available Properties');

        if ($this->country !== '') {
            $parts[] = $isArabic ? "في {$this->country}" : "in {$this->country}";
        }

        if ($this->search !== '') {
            $parts[] = $isArabic ? "— بحث: \"{$this->search}\"" : "— search: \"{$this->search}\"";
        }

        $brand = app(\App\Support\Brand::class);
        $title = implode(' ', $parts).' | '.$brand->name();

        $description = $isArabic
            ? 'تصفح '.implode(' ', $parts).' على '.$brand->name().' — بحث متقدم، صور حقيقية، وتواصل مباشر مع المعلن.'
            : 'Browse '.implode(' ', $parts).' on '.$brand->name().' — advanced search, real photos, and direct contact with the lister.';

        return [$title, \Illuminate\Support\Str::limit($description, 160)];
    }

    public function render()
    {
        [$title, $description] = $this->seoTitleAndDescription();

        return view('livewire.property-list', [
            'properties' => $this->getPropertiesProperty(),
            'amenities' => $this->propertyFeatureService->getFeatures(),
        ])->layout('layouts.app', [
            'title' => $title,
            'metaDescription' => $description,
        ]);
    }
    
    public function viewProperty($propertyId)
    {
        $property = Property::findOrFail($propertyId);
        Activity::create([
            'user_id' => auth()->id(),
            'type' => 'property_view',
            'description' => "Viewed property: {$property->title}",
            'property_id' => $propertyId,
        ]);
    
        $this->dispatch('updateRecommendations');
    }

    public function toggleFavorite($propertyId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            session()->flash('message', __('Property removed from wishlist'));
            $this->dispatch('favoriteRemoved');
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $propertyId,
                'team_id' => $user->currentTeam?->id,
            ]);
            session()->flash('message', __('Property added to wishlist'));
            $this->dispatch('favoriteAdded');
        }
    }

    public function isFavorited($propertyId)
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Favorite::where('user_id', Auth::id())
            ->where('property_id', $propertyId)
            ->exists();
    }
}
