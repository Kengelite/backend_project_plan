<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/v1'], function () {
    // Route::group( 'prefix' => '/admin'], function () {
    Route::group(['middleware' => ['auth:sanctum', 'ability:admin'], 'prefix' => '/admin'], function () {
        Route::resource('strategic', StrategicController::class);
        Route::resource('actionplan', ActionplanController::class);
        Route::resource('project', ProjectController::class);
        Route::resource('activity', ActivityController::class);
    });
});
