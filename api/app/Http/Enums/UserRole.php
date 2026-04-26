<?php

namespace App\Http\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
}
