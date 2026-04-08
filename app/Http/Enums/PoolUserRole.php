<?php

declare(strict_types=1);

namespace App\Http\Enums;

enum PoolUserRole: string
{
    case OWNER = 'OWNER';
    case ADMIN = 'ADMIN';
    case MEMBER = 'MEMBER';
}
