<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LineScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($lineId = session('active_line_id')) {
            $builder->where($model->getTable().'.line_id', (int) $lineId);
        }
    }
}
