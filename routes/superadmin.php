<?php

use App\Http\Controllers\Admin\Manage\StrategicController;
use App\Http\Controllers\Admin\Manage\ActionplanController;
use App\Http\Controllers\Admin\Manage\ActivityController;
use App\Http\Controllers\Admin\Manage\ActivitydetailController;
use App\Http\Controllers\Admin\Manage\ActivityDetailSpendMoneyController;
use App\Http\Controllers\Admin\Manage\ProjectController;
use App\Http\Controllers\Admin\Manage\ProjectUserController;
use App\Http\Controllers\Admin\Manage\YearController;
use App\Http\Controllers\Admin\Manage\UserController;
use App\Http\Controllers\Admin\Manage\OkrController;
use App\Http\Controllers\Admin\Manage\PrincipleController;
use App\Http\Controllers\Admin\Manage\TypeController;
use App\Http\Controllers\Admin\Manage\DepartmentController;
use App\Http\Controllers\Admin\Manage\PositionController;
use App\Http\Controllers\Admin\Manage\UnitController;
use App\Http\Controllers\Admin\Manage\EmailController;
use App\Http\Controllers\Admin\Manage\IndicatorController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\Manage\ObjectiveController;
use App\Http\Controllers\Admin\Manage\OkrActivityController;
use App\Http\Controllers\Admin\Manage\OkrProjectController;
use App\Http\Controllers\Admin\Manage\ObjectiveActivityController;
use App\Http\Controllers\Admin\Manage\IndicatorActivityController;
use App\Http\Controllers\Admin\Manage\UserActivityController;
use App\Http\Controllers\Admin\Manage\UserOkrController;
use App\Http\Controllers\Admin\Manage\ActivitySpendMoneyController;
use App\Http\Controllers\Admin\Manage\FileDataUploadController;
use App\Http\Controllers\Admin\Manage\RoundFileUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Models\ActivityDetailSpendmoney;

Route::group(['prefix' => '/v1'], function () {
    // Route::group( 'prefix' => '/admin'], function () {
    Route::group(['middleware' => ['auth:sanctum', 'ability:2'], 'prefix' => '/superadmin'], function () {

        Route::resource('yearall', YearController::class);
        Route::post('year', [YearController::class, 'yealUser']);
        Route::post('updatestatusyear', [YearController::class, 'updatestatus']);
        Route::delete('deleteyear', [YearController::class, 'destroy']);
        Route::post('yearallnew', [YearController::class, 'yearallnew']);
        Route::post('yearallnewokr', [YearController::class, 'yearallnewokr']);
        // Route::get('yearallnew', [YearController::class, 'getyearallnew']);
        Route::post('insertyearallnew', [YearController::class, 'insertyearallnew']);
        Route::post('insertyearallnewokr', [YearController::class, 'insertyearallnewokr']);



        Route::resource('okrall', OkrController::class);
        Route::post('okr', [OkrController::class, 'OkeUser']);
        Route::post('updatestatusokr', [OkrController::class, 'updatestatusOkr']);
        Route::delete('deleteokr', [OkrController::class, 'destroy']);
        Route::get('/okr-next-number', [OkrController::class, 'getNextOkrNumber']);

        Route::resource('okruser', UserOkrController::class);
        Route::post('okruserid', [UserOkrController::class, 'getactivityuseryear']);

        Route::resource('styleall', TypeController::class);
        Route::get('style', [TypeController::class, 'styleUser']);
        Route::post('updatestatusstyle', [TypeController::class, 'updatestatus']);
        Route::delete('deletestyle', [TypeController::class, 'destroy']);



        Route::resource('departmentall', DepartmentController::class);
        Route::get('department', [DepartmentController::class, 'DepartmentUser']);
        Route::post('updatestatusdepartment', [DepartmentController::class, 'updatestatus']);
        Route::delete('deletedepartment', [DepartmentController::class, 'destroy']);

        Route::resource('positionall', PositionController::class);
        Route::get('position', [PositionController::class, 'positionuser']);
        Route::post('updatestatusposition', [PositionController::class, 'updatestatus']);
        Route::delete('deleteposition', [PositionController::class, 'destroy']);


        Route::resource('principleall', PrincipleController::class);
        Route::get('principle', [PrincipleController::class, 'principleUser']);
        Route::post('updatestatusprinciple', [PrincipleController::class, 'updatestatusPrinciple']);
        Route::delete('deleteprinciple', [PrincipleController::class, 'destroy']);


        Route::resource('userall', UserController::class);
        Route::post('usersummarize', [UserController::class, 'getdataforuser']);
        Route::post('userresponsibleproject', [UserController::class, 'getdataforuserproject']);
        Route::post('userresponsibleactivity', [UserController::class, 'getdataforuseractivity']);
        Route::post('userresponsibleokr', [UserController::class, 'getdataforuserokr']);


        Route::get('userteacher', [UserController::class, 'teacher']);
        Route::get('useremployee', [UserController::class, 'employee']);
        Route::delete('deleteuser', [UserController::class, 'destroy']);


        Route::resource('strategic', StrategicController::class);
        Route::post('updatestatusstrategic', [StrategicController::class, 'updatestatusStrategic']);
        Route::post('yearstrategic', [StrategicController::class, 'getStrategicByYear']);
        Route::post('strategicforadd', [StrategicController::class, 'getStrategicByYearForAdd']);
        Route::delete('deletestrategic', [StrategicController::class, 'destroy']);
        Route::post('strategicsum', [StrategicController::class, 'getstrategicsum']);


        Route::resource('actionplan', ActionplanController::class);
        Route::post('actionplanbyidstrategic', [ActionplanController::class, 'actionplanByIdstrategic']);
        Route::post('actionplanallbyidyear', [ActionplanController::class, 'getByIdYear']);
        Route::post('actionplanprice', [ActionplanController::class, 'dataspendprice']);
        Route::post('updatestatusactionplan', [ActionplanController::class, 'updatestatusActionplan']);
        Route::delete('deleteactionplan', [ActionplanController::class, 'destroy']);


        Route::resource('project', ProjectController::class);
        Route::post('projectprice', [ProjectController::class, 'dataspendprice']);
        Route::post('projectbyidactionplan', [ProjectController::class, 'projectByIdactionplan']);
        Route::post('updatestatusproject', [ProjectController::class, 'updatestatusProject']);
        Route::delete('deleteproject', [ProjectController::class, 'destroy']);
        Route::post('projectallbyidyear', [ProjectController::class, 'getByIdYear']);
        Route::post('projectid', [ProjectController::class, 'projectById']);




        Route::resource('activity', ActivityController::class);
        Route::post('activityprice', [ActivityController::class, 'dataspendprice']);
        Route::post('activitybyidproject', [ActivityController::class, 'activityByIdproject']);
        Route::post('activitybyidprojectadmin', [ActivityController::class, 'activityByIdprojectAdmin']);
        Route::post('updatestatusactivity', [ActivityController::class, 'updatestatusActivity']);
        Route::post('activityuserallbyidyear', [ActivityController::class, 'getActivityUserYear']);
        Route::post('activitydetailbudget', [ActivityController::class, 'getactivitydetailbudget']);
        Route::delete('deleteactivity', [ActivityController::class, 'destroy']);
        Route::post('activityallbyidyear', [ActivityController::class, 'getByIdYear']);



        Route::resource('activitydetail', ActivitydetailController::class);
        Route::post('activitydetailprice', [ActivitydetailController::class, 'dataspendprice']);
        Route::post('activitydetailbyidactivity', [ActivitydetailController::class, 'activitydetailByIdactivity']);
        Route::post('activitydetailbyidactivityadmin', [ActivitydetailController::class, 'activitydetailByIdactivityAdmin']);
        Route::post('updatestatusactivitydetail', [ActivitydetailController::class, 'updatestatusActivityDetail']);
        Route::delete('deleteactivitydetail', [ActivitydetailController::class, 'destroy']);
        Route::get('activitydetail/price/{id}', [ActivitydetailController::class, 'datailprice']);

        Route::resource('projectuserall', ProjectUserController::class);
        Route::post('projectuserallbyiduseradmin', [ProjectUserController::class, 'getByIdUserYearAdmin']);
        Route::post('projectuserallbyiduser', [ProjectUserController::class, 'getByIdUser']);
        Route::post('projectuserallbyidyear', [ProjectUserController::class, 'getByIdYear']);
        Route::post('editprojectuser', [ProjectUserController::class, 'update']);
        Route::post('deleteprojectuser', [ProjectUserController::class, 'destroy']);


        Route::resource('unitall', UnitController::class);
        Route::get('unit', [UnitController::class, 'user']);


        Route::post('dashboard', [DashboardController::class, 'project']);
        Route::post('dashboardpie', [DashboardController::class, 'pie']);
        Route::post('dashboardbardepartment', [DashboardController::class, 'pie']);
        Route::post('dashboardlinestrategicreport', [DashboardController::class, 'LineStrategicReport']);
        Route::get('/send-mail/{id}/{type}', [EmailController::class, 'sendEmail']);

        Route::get('/testmail/{id}/{type}', [EmailController::class, 'testEmail']);

        Route::post('addobjective', [ObjectiveController::class, 'store']);
        Route::post('editobjective', [ObjectiveController::class, 'update']);
        Route::post('deleteobjective', [ObjectiveController::class, 'destroy']);

        Route::post('addokrproject', [OkrProjectController::class, 'store']);
        Route::post('editokrproject', [OkrProjectController::class, 'update']);
        Route::post('deleteokrproject', [OkrProjectController::class, 'destroy']);

        Route::resource('okractivity', OkrActivityController::class);

        Route::resource('objectiveactivity', ObjectiveActivityController::class);
        Route::resource('indicatoractivity', IndicatorActivityController::class);
        Route::resource('useractivity', UserActivityController::class);

        Route::post('addindicatorproject', [IndicatorController::class, 'store']);
        Route::post('editindicatorproject', [IndicatorController::class, 'update']);
        Route::post('deleteindicatorproject', [IndicatorController::class, 'destroy']);

        Route::resource('activityspendmoney', ActivitySpendMoneyController::class);
        Route::get('/activityspendmoney/activity/{id}', [ActivitySpendmoneyController::class, 'getbyidactivity']);

        Route::resource('activitydetailspendmoney', ActivityDetailSpendMoneyController::class);




        Route::resource('filedataupload', FileDataUploadController::class);

        Route::post('filedatabyidfile', [FileDataUploadController::class, 'getallfileround']);

        Route::resource('roundfileupload', RoundFileUploadController::class);
        Route::post('uploaddata', [RoundFileUploadController::class, 'uploaddata']);
    });
});
