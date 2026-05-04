<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'description',
        'website_url',
        'is_active',
        'contacts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'contacts' => 'array',
    ];

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_platform')
            ->withPivot('custom_message', 'is_active')
            ->withTimestamps();
    }
}
