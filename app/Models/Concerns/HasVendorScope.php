<?php

namespace App\Models\Concerns;

use App\Models\Scopes\VendorScope;

trait HasVendorScope
{
    protected static function bootHasVendorScope(): void
    {
        static::addGlobalScope(new VendorScope);

        static::creating(function ($model): void {
            if (empty($model->vendor_id) && ($vendorId = session('active_vendor_id'))) {
                $model->vendor_id = (int) $vendorId;
            }
        });
    }
}
