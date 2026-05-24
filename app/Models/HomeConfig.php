<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class HomeConfig extends Model
{
    use HasVendorScope;

    protected $table = 'home_config';

    protected $fillable = [
        'vendor_id',
        'section',
        'item_id',
        'order',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    const SECTION_CAROUSEL = 'carousel';

    const SECTION_BONUSES = 'bonuses';

    const SECTION_BLOG = 'blog';

    const SECTION_RAFFLES_ACTIVE = 'raffles_active';

    const SECTION_RAFFLES_UPCOMING = 'raffles_upcoming';
}
