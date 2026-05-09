<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'line_id',
        'agent_id',
        'client_id',
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

    public static function getMeses(): array
    {
        return [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
