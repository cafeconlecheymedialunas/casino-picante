<?php

namespace App\Models;

use App\Models\Scopes\LineScope;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new LineScope);
    }

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
