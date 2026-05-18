<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
        'name',
        'type',
        'phone',
        'icon',
        'description',
        'status',
        'contact_links',
        'best_sales',
        'portada_url',
        'perfil_url',
    ];

    protected $casts = [
        'contact_links' => 'array',
        'platforms' => 'array',
        'best_sales' => 'decimal:2',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lineAgents()
    {
        return $this->hasMany(LineAgent::class);
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'line_agents')
            ->withPivot(['vendor_id', 'role', 'is_active', 'parent_id'])
            ->withTimestamps();
    }

    public function activeAgents()
    {
        return $this->agents()->wherePivot('is_active', true);
    }

    public function clients()
    {
        return $this->belongsToMany(User::class, 'line_clients')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function managers()
    {
        return $this->agents()->wherePivot('role', 'encargado')->wherePivot('is_active', true);
    }

    public function encargados()
    {
        return $this->agents()->wherePivot('role', 'encargado')->wherePivot('is_active', true);
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'line_platform')
            ->withPivot('vendor_id', 'custom_message', 'is_active')
            ->withTimestamps();
    }

    public function activePlatforms()
    {
        return $this->platforms()->wherePivot('is_active', true);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function ratings()
    {
        return $this->hasMany(LineRating::class);
    }

    public function getAverageRatingAttribute()
    {
        $avg = $this->ratings()->avg('rating');
        return $avg ? round($avg, 1) : 0;
    }

    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }
}
