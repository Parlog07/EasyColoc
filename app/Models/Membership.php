<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\user;
use App\Models\Colocatio;

class Membership extends Model
{
    protected $fillable = ['user_id', 'colocation_id', 'role', 'joined_at', 'left_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }
}
