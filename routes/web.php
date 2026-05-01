<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Users\UsersIndex;

Route::get('/usuarios', UsersIndex::class)->name('users.index');

Route::get('/', function () {
    return view('welcome');
});