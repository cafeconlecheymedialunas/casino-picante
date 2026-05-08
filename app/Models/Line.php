<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
        'icon',
        'description',
        'status',
        'encargado_id',
        'contact_links',
        'permissions',
        'best_sales',
        'portada_url',
        'perfil_url',
        'mejor_mes',
        'mejor_mes_total',
        'mejor_plataforma',
        'mejor_plataforma_total',
        'ventas_mes_actual',
        'ventas_mes_pasado',
        'ventas_mes_antiguo',
        'ganancia_encargado',
        'porcentaje_encargado',
    ];

    protected $casts = [
        'contact_links' => 'array',
        'platforms' => 'array',
        'permissions' => 'array',
        'best_sales' => 'decimal:2',
    ];

    public function lineAgents()
    {
        return $this->hasMany(LineAgent::class);
    }

    public function agents()
    {
        return $this->belongsToMany(Agent::class, 'line_agents')
            ->withPivot(['role', 'is_active', 'parent_id'])
            ->withTimestamps();
    }

    public function activeAgents()
    {
        return $this->agents()->wherePivot('is_active', true);
    }

    public function clients()
    {
        return $this->belongsToMany(User::class, 'line_clients')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function managers()
    {
        return $this->agents()->wherePivot('role', 'encargado')->wherePivot('is_active', true);
    }

    public function encargados()
    {
        return $this->agents()->wherePivot('role', 'encargado')->wherePivot('is_active', true);
    }

    public function encargado()
    {
        return $this->belongsTo(Agent::class, 'encargado_id');
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'line_platform')
            ->withPivot('custom_message', 'is_active')
            ->withTimestamps();
    }

    public function activePlatforms()
    {
        return $this->platforms()->wherePivot('is_active', true);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
