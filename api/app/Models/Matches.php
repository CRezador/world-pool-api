<?php

namespace App\Models;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matches extends Model
{
    use HasFactory;

    protected $table = 'matches';
    protected $fillable = [
        'kickoff_at',
        'game_day',
        'stage',
        'group_id',
        'status',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
    ];
    protected $casts = [
        'kickoff_at' => 'datetime',
        'stage' => MatchStage::class,
        'status' => MatchStatus::class,
    ];
    protected $connection = 'mysql';

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
