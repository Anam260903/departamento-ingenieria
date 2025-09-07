<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
route::get('/ejemplo', function () {
    return view('ejemplo');
});