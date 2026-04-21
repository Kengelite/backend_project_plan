<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use App\Http\Controllers\Admin\Manage\ProjectUserController;
use App\Http\Controllers\Admin\Manage\ActivitydetailController;
use App\Http\Controllers\Admin\Manage\UserOkrController;
use App\Http\Controllers\Admin\Manage\ActivitySpendMoneyController;
use App\Http\Controllers\Admin\Manage\ActivityDetailSpendMoneyController;
use App\Http\Controllers\Admin\Manage\UnitController;
use App\Http\Controllers\Admin\Manage\OkrReportController;
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
        Route::get('activity/responsible/project', [ActivityController::class, 'getActivityResponsibleByProject']);

        Route::resource('okruser', UserOkrController::class);
        Route::post('okruserid', [UserOkrController::class, 'getactivityuseryear']);

        Route::resource('activityspendmoney', ActivitySpendMoneyController::class);
        Route::get('/activityspendmoney/activity/{id}', [ActivitySpendmoneyController::class, 'getbyidactivity']);

        Route::resource('activitydetailspendmoney', ActivityDetailSpendMoneyController::class);

        Route::resource('unitall', UnitController::class);
        Route::get('unit', [UnitController::class, 'user']);

        Route::resource('okrreport', OkrReportController::class);
    });
});
