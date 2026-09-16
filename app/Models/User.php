<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JoelButcher\Socialstream\HasConnectedAccounts;
use JoelButcher\Socialstream\SetsProfilePhotoFromUrl;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants
{
    use HasApiTokens;

    use HasFactory;
    use HasProfilePhoto {
        HasProfilePhoto::profilePhotoUrl as getPhotoUrl;
    }
    // use HasConnectedAccounts;
    use HasRoles;
    use HasTeams;

    use Notifiable;
    // use SetsProfilePhotoFromUrl;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agent_preferences',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'agent_preferences' => 'array',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function profilePhotoUrl(): Attribute
    {
        return filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)
            ? Attribute::get(fn () => $this->profile_photo_path)
            : $this->getPhotoUrl();
    }

    /**
     * @return array<Model> | Collection
     */
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        return $this->canAccessPanelById($panelId);
    }

    public function canAccessPanelById(string $panelId): bool
    {
        if ($this->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }

        // The "app" panel is the only one with ->registration() enabled
        // (AppPanelProvider) — a general, open self-service portal for
        // anyone who signs up without a pre-assigned role. filament-shield's
        // config still gated it to admin/super_admin/staff like every other
        // (internal, role-assigned) panel, which meant every freshly
        // self-registered user was immediately locked out with a 403 on the
        // very panel they just registered into. Scoped to roleless users
        // only — a user who already holds one of the 8 persona roles stays
        // confined to their own panel (PanelSwitcherTest's "buyer sees no
        // switch links" encodes exactly that boundary; granting 'app' to
        // every authenticated user broke it).
        if ($panelId === 'app' && $this->roles()->doesntExist()) {
            return true;
        }

        $allowedRoles = config("filament-shield.panels.$panelId", []);

        return $this->hasAnyRole($allowedRoles);
    }

    public function canAccessFilament(): bool
    {
        return true;
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestTeam;
    }

    public function latestTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, Membership::class)
            ->withPivot(['role', 'permissions', 'branch_id', 'department_id', 'job_title', 'phone', 'bio', 'is_public'])
            ->withTimestamps()
            ->as('membership');
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class);
    }

    /**
     * Saved property alerts (see App\Jobs\CheckPropertyAlerts).
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewsReceived()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function rentals()
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function assignedAgencyTasks()
    {
        return $this->hasMany(AgencyTask::class, 'assigned_to');
    }

    public function negotiatedOffers()
    {
        return $this->hasMany(Offer::class, 'negotiator_id');
    }

    public function averageRating()
    {
        return $this->reviewsReceived()->avg('rating');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id')
            ->withTimestamps();
    }

    public function team()
    {
        return $this->currentTeam();
    }
}
