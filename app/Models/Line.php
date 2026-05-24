<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Line extends Model
{
    use HasVendorScope;

    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
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

    protected static function booted(): void
    {
        static::saving(function (Line $line) {
            if (blank($line->slug)) {
                $line->slug = static::uniqueSlug($line->name ?: 'linea', $line->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::withoutGlobalScopes()
            ->where($field ?: $this->getRouteKeyName(), $value)
            ->first();
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'linea';
        $slug = $base;
        $suffix = 2;

        while (static::withoutGlobalScopes()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

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
            ->withoutGlobalScopes()
            ->withPivot('vendor_id', 'custom_message', 'is_active')
            ->withTimestamps();
    }

    public function activePlatforms()
    {
        return $this->platforms()
            ->wherePivot('is_active', true)
            ->where('platforms.is_active', true);
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
