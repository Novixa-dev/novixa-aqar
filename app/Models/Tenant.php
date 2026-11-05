<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tenants';

    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password', 'phone', 'team_id',
        'user_id', 'search_criteria', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'search_criteria' => 'array',
    ];

    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name.' '.$this->last_name;
        }

        return $this->name ?? '';
    }

    // Lease.tenant_id and Payment.tenant_id both point to users.id, not
    // tenants.id (see CLAUDE.md) -- the default hasMany would join against
    // this Tenant row's own id instead, which is a different id space and
    // would silently return the wrong rows (or none). Pass this Tenant's
    // user_id as the local key so the join actually matches.
    public function leases()
    {
        return $this->hasMany(Lease::class, 'tenant_id', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id', 'user_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyMatches()
    {
        return $this->hasMany(PropertyMatch::class);
    }
}
