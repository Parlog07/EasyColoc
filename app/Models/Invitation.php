<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = [
        'colocation_id',
        'email',
        'token',
        'status',
        'expires_at',
        'accepted_by_user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }
}
