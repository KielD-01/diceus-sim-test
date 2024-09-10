<?php

namespace App\Enums\Games;

enum GameStatusesEnum: string
{
    case SCHEDULED = 'scheduled';
    case PLAYING = 'playing';
    case FINISHED = 'finished';
}
