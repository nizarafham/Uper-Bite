<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/reg', function () {
    return view('register');
});
Route::get('/log', function () {
    return view('login');
});
