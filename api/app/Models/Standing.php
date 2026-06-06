<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    protected $table = 'standings';
    protected $connection = 'mysql';

    protected $fillable = [
        'group',
        'position',
        'team_id',
        'played',
        'won',
        'draw',
        'lost',
        'goals_for',
        'goals_against',
        'goal_diff',
        'points',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
