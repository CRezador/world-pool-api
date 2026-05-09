<?php

namespace App\Models;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoolMember extends Model
{
    use HasFactory;

    protected $table = 'pool_members';
    public $timestamps = false;

    protected $fillable = [
        'pool_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];
    protected $casts = [
        'joined_at' => 'datetime',
        'status' => PoolMemberStatus::class,
        'role' => PoolUserRole::class,
    ];
    protected $connection = 'mysql';

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pool::class, 'pool_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
