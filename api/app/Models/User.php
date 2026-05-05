<?php

namespace App\Models;

use App\Http\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'role' => UserRole::class,
    ];
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}
