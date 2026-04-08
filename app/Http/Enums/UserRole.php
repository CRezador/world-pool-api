<?php

declare(strict_types=1);

namespace App\Http\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
}
