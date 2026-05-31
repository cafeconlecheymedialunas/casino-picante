<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    use HasVendorScope;

    protected bool $preserveNullVendorId = true;

    protected $fillable = [
        'vendor_id',
        'image',
        'title',
        'description',
        'cta_text',
        'link',
        'order',
        'line_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
