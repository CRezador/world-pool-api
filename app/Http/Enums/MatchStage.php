<?php

namespace App\Http\Enums;

enum MatchStage: string
{
    case GROUP_STAGE = 'GROUP_STAGE';
    case ROUND_OF_16 = 'ROUND_OF_16';
    case QUARTER_FINALS = 'QUARTER_FINALS';
    case SEMI_FINALS = 'SEMI_FINALS';
    case THIRD_PLACE = 'THIRD_PLACE';
    case FINAL = 'FINAL';
}
