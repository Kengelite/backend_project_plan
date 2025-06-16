<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use App\Http\Controllers\Admin\Manage\ProjectUserController;
use App\Http\Controllers\Admin\Manage\ActivitydetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Manage\YearController;


Route::group(['prefix' => '/v1'], function () {
    // Route::group( 'prefix' => '/admin'], function () {
    Route::group(['middleware' => ['auth:sanctum', 'ability:1'], 'prefix' => '/user'], function () {
        Route::resource('strategic', StrategicController::class);
        Route::post('projectuserallbyidyear', [ProjectUserController::class, 'getByIdYear']);
        Route::post('activitybyidproject', [ActivityController::class, 'activityByIdproject']);
        Route::post('activitydetailbyidactivity', [ActivitydetailController::class, 'activitydetailByIdactivity']);
        Route::post('year', [YearController::class, 'yealUser']);
        Route::post('activityuserallbyidyear', [ActivityController::class, 'getActivityUserYear']);
        Route::resource('activitydetail', ActivitydetailController::class);
    });
});
