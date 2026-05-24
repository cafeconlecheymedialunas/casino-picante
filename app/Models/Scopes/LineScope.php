<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * LineScope is intentionally NOT registered as a global scope.
 * Line-based filtering is handled by the AuthorizeLine middleware,
 * which resolves the active line from session and validates permissions.
 *
 * To use this scope manually on a model:
 *   static::addGlobalScope(new LineScope);
 * or in a query:
 *   Model::withGlobalScope(new LineScope)->get();
 */
class LineScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($lineId = session('active_line_id')) {
            $builder->where($model->getTable().'.line_id', (int) $lineId);
        }
    }
}
