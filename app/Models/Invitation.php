<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
