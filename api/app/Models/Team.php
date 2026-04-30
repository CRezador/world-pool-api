<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';
    protected $fillable = ['name', 'group', 'code'];
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
