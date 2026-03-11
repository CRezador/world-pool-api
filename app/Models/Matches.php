<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Http\Enums\MatchStage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Matches extends Authenticatable
{
    use MatchStage, HasFactory, Notifiable, HasApiTokens;

    protected $table = 'matches';
    protected $fillable = [
        'home_score',
        'away_score',
        'kickoff_at',
    ];
    protected $casts = [
        'kickoff_at' => 'datetime',
        'stage' => MatchStage::class,
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
