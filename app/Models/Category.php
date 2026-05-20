<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasVendorScope;

    protected $fillable = ['vendor_id', 'name', 'slug'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
