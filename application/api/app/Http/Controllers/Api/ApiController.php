<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct()
    {
        request()->headers->set('Access-Control-Allow-Origin', 'diceus-sim.local');
        request()->headers->set('Content-Type', 'application/json');
        request()->headers->set('Accept', 'application/json');
    }
}
