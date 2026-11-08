<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Membership as JetstreamMembership;

class Membership extends JetstreamMembership
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $fillable = [
        'role',
        'permissions',
        'branch_id',
        'department_id',
        'job_title',
        'phone',
        'bio',
        'is_public',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_public' => 'boolean',
    ];
}
