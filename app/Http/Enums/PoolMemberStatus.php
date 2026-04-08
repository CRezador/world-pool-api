<?php

declare(strict_types=1);

namespace App\Http\Enums;

enum PoolMemberStatus: string
{
    case ACTIVE = 'ACTIVE';
    case LEFT = 'LEFT';
    case BANNED = 'BANNED';
}
