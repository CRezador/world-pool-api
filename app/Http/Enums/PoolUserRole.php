<?php

namespace App\Http\Enums;

enum PoolUserRole: string
{
    case OWNER = 'OWNER';
    case ADMIN = 'ADMIN';
    case MEMBER = 'MEMBER';
}
