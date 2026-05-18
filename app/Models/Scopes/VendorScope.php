<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VendorScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Solo aplicar si hay un vendor activo en la sesión
        // y el modelo tiene la columna vendor_id
        if ($vendorId = session('active_vendor_id')) {
            $authUserId = session(auth()->getName());

            if ($model->getTable() === 'users' && $authUserId) {
                $builder->where(function (Builder $query) use ($model, $vendorId, $authUserId): void {
                    $query->where($model->getTable().'.vendor_id', (int) $vendorId)
                        ->orWhere($model->getTable().'.id', (int) $authUserId);
                });

                return;
            }

            $builder->where($model->getTable().'.vendor_id', (int) $vendorId);
        }
    }
}
