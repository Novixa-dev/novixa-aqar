<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'team_id',
        'property_id',
        'type',
        'title',
        'alt_text',
        'file_path',
        'file_name',
        'mime_type',
        'disk',
        'file_size',
        'sort_order',
        'is_primary',
        'is_public',
        'watermark',
        'metadata',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_public' => 'boolean',
        'watermark' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return asset('storage/'.$this->file_path);
        }

        return null;
    }
}
