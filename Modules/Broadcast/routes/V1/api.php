<?php

use Broadcast\Http\Controllers\AuthController;

Route::group(['prefix' => '/auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
});
