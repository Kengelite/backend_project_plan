<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ActivitydetailController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use App\Http\Controllers\Admin\Manage\ProjectUserController;
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
        Route::delete('deletestrategic', [StrategicController::class, 'destroy']);


        Route::post('actionplanbyidstrategic', [ActionplanController::class, 'actionplanByIdstrategic']);
        Route::post('actionplanallbyidyear', [ActionplanController::class, 'getByIdYear']);
        Route::resource('actionplan', ActionplanController::class);
        Route::post('updatestatusactionplan', [ActionplanController::class, 'updatestatusActionplan']);
        Route::delete('deleteactionplan', [ActionplanController::class, 'destroy']);


        Route::resource('project', ProjectController::class);
        Route::post('projectbyidactionplan', [ProjectController::class, 'projectByIdactionplan']);
        Route::post('updatestatusproject', [ProjectController::class, 'updatestatusProject']);
        Route::delete('deleteproject', [ProjectController::class, 'destroy']);
        Route::post('projectallbyidyear', [ProjectController::class, 'getByIdYear']);


        Route::resource('activity', ActivityController::class);
        Route::post('activitybyidproject', [ActivityController::class, 'activityByIdproject']);
        Route::post('activitybyidprojectAdmin', [ActivityController::class, 'activityByIdprojectAdmin']);
        Route::post('updatestatusactivity', [ActivityController::class, 'updatestatusActivity']);
        Route::post('activityuserallbyidyear', [ActivityController::class, 'getActivityUserYear']);
        Route::delete('deleteactivity', [ActivityController::class, 'destroy']);



        Route::resource('activitydetail', ActivitydetailController::class);
        Route::post('activitydetailbyidactivity', [ActivitydetailController::class, 'activitydetailByIdactivity']);
        Route::post('activitydetailbyidactivityadmin', [ActivitydetailController::class, 'activitydetailByIdactivityAdmin']);
        Route::post('updatestatusactivitydetail', [ActivitydetailController::class, 'updatestatusActivityDetail']);
        Route::delete('deleteactivitydetail', [ActivitydetailController::class, 'destroy']);



        Route::resource('projectuserall', ProjectUserController::class);
        Route::post('projectuserallbyiduseradmin', [ProjectUserController::class, 'getByIdUserYearAdmin']);
        Route::post('projectuserallbyiduser', [ProjectUserController::class, 'getByIdUser']);
        Route::post('projectuserallbyidyear', [ProjectUserController::class, 'getByIdYear']);

    });
});
