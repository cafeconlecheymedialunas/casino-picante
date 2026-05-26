<?php

namespace App\Models;

use App\Models\Concerns\HasVendorScope;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasVendorScope, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'vendor_id',
        'username',
        'name',
        'apellido',
        'avatar',
        'line_id',
        'email',
        'password',
        'phone',
        'contact',
        'wants_bonus_emails',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wants_bonus_emails' => 'boolean',
        ];
    }

    public function preferredLine()
    {
        return $this->belongsTo(Line::class, 'line_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function assignedVendor()
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class, 'line_clients')
            ->withPivot('vendor_id', 'is_active')
            ->withTimestamps();
    }
}
