<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response('Client portal — not yet implemented', 501);
})->name('home');
