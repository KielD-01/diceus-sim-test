<?php

namespace App\Enums\Games;

enum GameResultsEnum: string
{
    case WINS = 'wins';
    case DRAW = 'draws';
    case LOSE = 'losses';
}
