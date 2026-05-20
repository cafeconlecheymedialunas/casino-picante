<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_platform')
            ->withPivot('vendor_id', 'custom_message', 'is_active')
            ->withTimestamps();
    }
}
