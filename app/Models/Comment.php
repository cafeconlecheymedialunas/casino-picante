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
        'parent_id',
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

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user')->orderBy('created_at');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }
}
