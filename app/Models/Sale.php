<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'line_id',
        'platform_id',
        'fecha',
        'descripcion',
        'monto_fichas',
        'ganancia_superagente',
    ];

    protected $casts = [
        'fecha'                => 'date',
        'monto_fichas'         => 'decimal:2',
        'ganancia_superagente' => 'decimal:2',
    ];

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
