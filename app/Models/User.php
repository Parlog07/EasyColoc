<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_banned',
        'reputation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'reputation' => 'integer',
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function colocations(): BelongsToMany
    {
        return $this->belongsToMany(Colocation::class, 'memberships')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function activeMembership()
    {
        return $this->hasOne(Membership::class)->whereNull('left_at');
    }

    public function expensesPaid(): HasMany
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }
}
