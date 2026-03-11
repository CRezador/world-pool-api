<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Leaderboard extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'leaderboard';
    protected $fillable = [
        'points',
        'exact_hits',
        'result_hits',
        'guesses_count',
    ];
    protected $casts = [
        'updated_at' => 'datetime',
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
