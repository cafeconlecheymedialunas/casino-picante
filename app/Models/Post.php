<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);
    }

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'type',
        'status',
        'published_at',
        'line_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    const TYPE_NOVEDAD = 'novedad';

    const TYPE_BLOG = 'blog';

    const TYPE_CARRUSEL = 'carrusel';

    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_HIDDEN = 'hidden';

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    public function pendingComments()
    {
        return $this->hasMany(Comment::class)->where('is_approved', false);
    }
}
