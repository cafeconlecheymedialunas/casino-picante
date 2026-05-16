<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeConfig extends Model
{
    protected $table = 'home_config';

    protected $fillable = [
        'section',
        'item_id',
        'order',
    ];

    const SECTION_CAROUSEL = 'carousel';
    const SECTION_BONUSES = 'bonuses';
    const SECTION_BLOG = 'blog';
    const SECTION_RAFFLES_ACTIVE = 'raffles_active';
    const SECTION_RAFFLES_UPCOMING = 'raffles_upcoming';
}
