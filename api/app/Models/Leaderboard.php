<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    use HasFactory;

    protected $table = 'leaderboard';
    protected $fillable = [
        'pool_id',
        'user_id',
        'points',
        'exact_hits',
        'result_hits',
        'guesses_count',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime',
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
