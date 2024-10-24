<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('https://' . env('SANCTUM_STATEFUL_DOMAINS'));
});

// Route::get('/{vue_capture?}', function () {
//     return view('welcome');
// })->where('vue_capture', '[\/\w\.-]*');
