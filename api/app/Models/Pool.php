<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pool extends Model
{
    use HasFactory;

    protected $table = 'pools';
    protected $fillable = [
        'name',
        'join_code',
        'is_public',
        'owner_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $connection = 'mysql';

    public function members(): HasMany
    {
        return $this->hasMany(PoolMembers::class, 'pool_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
