<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasVendorScope;

    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);
    }

    protected $fillable = [
        'vendor_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'status',
        'published_at',
        'line_id',
        'category_id',
        'author_agent_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScopes();
    }

    public function authorAgent()
    {
        return $this->belongsTo(Agent::class, 'author_agent_id');
    }

    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_HIDDEN = 'hidden';

    public function comments()
    {
        return $this->hasMany(Comment::class)->withoutGlobalScopes()->where('is_approved', true);
    }

    public function pendingComments()
    {
        return $this->hasMany(Comment::class)->withoutGlobalScopes()->where('is_approved', false);
    }
}
