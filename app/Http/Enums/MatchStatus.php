<?php

declare(strict_types=1);

namespace App\Http\Enums;

enum MatchStatus: string
{
    case SCHEDULED = 'SCHEDULED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case FINISHED = 'FINISHED';
}
