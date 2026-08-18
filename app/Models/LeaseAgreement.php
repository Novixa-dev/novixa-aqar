<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class LeaseAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'landlord_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'rent_amount',
        'security_deposit',
        'status',
        'payment_frequency',
        'terms',
        'content',
        'terms_and_conditions',
        'is_signed',
        'landlord_signed',
        'tenant_signed',
        'team_id',
        'deposit_scheme',
        'deposit_reference',
        'renewal_of_id',
        'notice_type',
        'notice_served_at',
        'notice_expires_at',
        'ended_at',
        'end_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_signed' => 'boolean',
        'landlord_signed' => 'boolean',
        'tenant_signed' => 'boolean',
        'monthly_rent' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'notice_served_at' => 'date',
        'notice_expires_at' => 'date',
        'ended_at' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = filled($value) ? Crypt::encryptString($value) : null;
    }

    public function getContentAttribute($value)
    {
        return filled($value) ? Crypt::decryptString($value) : null;
    }

    public function isFullySigned(): bool
    {
        return $this->landlord_signed && $this->tenant_signed;
    }
}
