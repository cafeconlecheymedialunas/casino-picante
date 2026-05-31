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
            if (
                property_exists($model, 'preserveNullVendorId') &&
                $model->preserveNullVendorId === true &&
                array_key_exists('vendor_id', $model->getAttributes()) &&
                $model->vendor_id === null
            ) {
                return;
            }

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
