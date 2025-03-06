<?php

use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Agent::isDesktop() ? view('desktop') : view('mobile');
});

Route::fallback(function () {
    return Agent::isDesktop() ? view('desktop') : view('mobile');
});
