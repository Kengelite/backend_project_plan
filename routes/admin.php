<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/v1'], function () {

    Route::get('/test', function (Request $request) {
        return 'test';
    });

    Route::group(['prefix' => '/admin'], function () {
        Route::resource('strategic', StrategicController::class);
        Route::resource('Actionplan', ActionplanController::class);
    });

    // Route::group(['prefix' => '/admin'], function () {
    //     Route::resource('Actionplan', ActionplanController::class);
    // });
});
