<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasFactory;

    /** Código do time sentinela que representa uma vaga ainda não definida (mata-mata). */
    public const TBD_CODE = 'TBD';

    protected $table = 'teams';
    protected $fillable = ['name', 'code', 'tla', 'group_id'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $connection = 'mysql';

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
