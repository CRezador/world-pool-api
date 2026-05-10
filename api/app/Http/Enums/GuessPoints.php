<?php

namespace App\Http\Enums;

enum GuessPoints: int
{
    case EXACT  = 3;
    case RESULT = 1;
    case MISS   = 0;
}
