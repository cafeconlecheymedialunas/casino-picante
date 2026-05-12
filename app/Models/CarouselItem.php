<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    protected $fillable = [
        'image',
        'title',
        'link',
        'order',
        'line_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
