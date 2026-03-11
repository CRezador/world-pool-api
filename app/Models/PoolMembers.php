<?php

namespace App\Models;

use App\Http\Enums\Role;
use App\Http\Enums\PoolMemberStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoolMembers extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'pool_members';
    protected $casts = [
        'joined_at' => 'datetime',
        'status' => Role::class,
        'role' => PoolMemberStatus::class,
    ];
    protected $connection = 'mysql';

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pools::class, 'pool_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
