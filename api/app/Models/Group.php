<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Group extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $table = 'groups';
    protected $fillable = ['name'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $connection = 'mysql';

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
