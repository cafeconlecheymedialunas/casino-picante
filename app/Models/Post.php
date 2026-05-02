<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'type',
        'status',
        'published_at',
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
}
