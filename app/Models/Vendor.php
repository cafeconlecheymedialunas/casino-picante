<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'hero_image',
        'portrait_image',
        'description',
        'contacts',
        'features',
        'branding',
        'is_active',
    ];

    protected $casts = [
        'contacts' => 'array',
        'features' => 'array',
        'branding' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(Line::class);
    }
}
