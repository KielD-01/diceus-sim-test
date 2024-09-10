<?php

use App\Http\Controllers\Api\League\DataController;
use App\Http\Controllers\Api\League\LeagueController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('v1.')
    ->group(function () {
        Route::prefix('data')
            ->name('data.')
            ->group(function () {
                Route::any('stream', [DataController::class, 'dataStream'])->name('stream');
            });

        Route::prefix('league')
            ->group(function () {
                Route::get('', [DataController::class, 'league'])->name('league');
                Route::prefix('{league}')
                    ->group(function () {
                        Route::post('play', [LeagueController::class, 'play'])->name('league.play');
                        Route::post('next-week', [LeagueController::class, 'nextWeek'])->name('league.next-week');
                    });
            });
    });
