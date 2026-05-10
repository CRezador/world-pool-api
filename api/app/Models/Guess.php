<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guess extends Model
{
    use HasFactory;

    protected $table = 'guesses';
    protected $fillable = [
        'user_id',
        'pool_id',
        'match_id',
        'home_score',
        'away_score',
        'points',
    ];
    protected $casts = [
        'home_score' => 'integer',
        'away_score' => 'integer',
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }
}
