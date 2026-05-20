<?php

namespace App\Models\Concerns;

use App\Models\Scopes\VendorScope;
use App\Support\Roles;
use Illuminate\Validation\ValidationException;

trait HasVendorScope
{
    protected static function bootHasVendorScope(): void
    {
        static::addGlobalScope(new VendorScope);

        static::creating(function ($model): void {
            if (auth()->user()?->hasRole(Roles::ADMIN) && ! session('active_vendor_id')) {
                throw ValidationException::withMessages([
                    'vendor_id' => 'Selecciona un vendor antes de crear contenido.',
                ]);
            }

            if (empty($model->vendor_id) && ($vendorId = session('active_vendor_id'))) {
                $model->vendor_id = (int) $vendorId;
            }
        });
    }
}
