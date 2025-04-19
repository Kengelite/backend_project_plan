<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use App\Http\Controllers\Admin\Manage\YearController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/v1'], function () {
    // Route::group( 'prefix' => '/admin'], function () {
    Route::group(['middleware' => ['auth:sanctum', 'ability:0'], 'prefix' => '/admin'], function () {

        Route::resource('year', YearController::class);


        Route::resource('strategic', StrategicController::class);
        Route::post('updatestatusstrategic', [StrategicController::class, 'updatestatusStrategic']);
        Route::post('yearstrategic', [StrategicController::class, 'getStrategicByYear']);

        Route::post('actionplanbyidstrategic', [ActionplanController::class, 'actionplanByIdstrategic']);
        Route::resource('actionplan', ActionplanController::class);
        Route::post('updatestatusactionplan', [ActionplanController::class, 'updatestatusActionplan']);


        Route::resource('project', ProjectController::class);
        Route::post('projectbyidactionplan', [ProjectController::class, 'projectByIdactionplan']);
        Route::post('updatestatusproject', [ProjectController::class, 'updatestatusProject']);


        Route::resource('activity', ActivityController::class);
        Route::post('activitybyidproject', [ActivityController::class, 'activity_by_idproject']);
    });
});
