<?php

namespace App\Http\Enums;

enum PoolMemberStatus: string
{
    case ACTIVE = 'ACTIVE';
    case LEFT = 'LEFT';
    case BANNED = 'BANNED';
}
