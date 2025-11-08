<?php

namespace App\Trait;

use App\Dto\ActionPlanDTO;
use App\Http\Requests\Admin\Manage\ProjectRequest;
use App\Http\Requests\Admin\Manage\DepartmentRequest;
use App\Http\Requests\Admin\Manage\TypeRequest;
use App\Http\Requests\Admin\Manage\PrincipleRequest;
use App\Http\Requests\Admin\Manage\PositionRequest;
use App\Http\Requests\Admin\Manage\YearRequest;
use App\Http\Requests\Admin\Manage\StrategicRequest;
use App\Http\Requests\Admin\Manage\ActionplanRequest;
use App\Http\Requests\Admin\Manage\ActivitydetailRequest;
use App\Http\Requests\Admin\Manage\ActivityDetailSpendMoneyRequest;
use App\Http\Requests\Admin\Manage\ProjectEditRequest;
use App\Http\Requests\Admin\Manage\ActivityEditRequest;
use App\Dto\ObstacleDTO;
use App\Dto\OkrDetailProjectDTO;
use App\Dto\PrincipleDTO;
use App\Dto\ProjectDTO;
use App\Dto\DepartmentDTO;
use App\Dto\ResultDTO;
use App\Dto\OkrDTO;
use App\Dto\TypeDTO;
use App\Dto\StyleDetailDTO;
use App\Dto\PositionDTO;
use App\Dto\YearDTO;
use App\Dto\StrategicDTO;
use App\Dto\ActivityDetailDTO;
use App\Dto\ActivityDTO;
use App\Dto\EmployeeDTO;
use App\Dto\IndicatorActivityDTO;
use App\Dto\IndicatorDTO;
use App\Dto\ActivitySpendMoneyDTO;
use App\Dto\ActivitiyDetailSpendMoneyDTO;
use App\Dto\ObjectiveDTO;
use App\Dto\StyleActivtiyDetailDTO;
use App\Dto\TeacherDTO;
use App\Dto\ProjectEditDTO;
use App\Dto\UserDTO;
use App\Http\Requests\Admin\Manage\ActivitySpendMoneyRequest;
use App\Http\Requests\Admin\Manage\UserRequest;
use App\Http\Requests\Admin\Manage\OkrRequest;

use App\Models\Principle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait Utils
{
    public function activityRequestToActivityDTO(Request $request)
    {
        $projectDTO = new ActivityDTO();

        $projectDTO->nameActivity = $request->input('activity_name');
        $projectDTO->idStrategic = $request->input('id_strategic');
        $projectDTO->abstract = $request->input('abstract');
        $projectDTO->location = $request->input('location');
        $projectDTO->timeStart = $request->input('time_start');
        $projectDTO->timeEnd = $request->input('time_end');
        $projectDTO->idDepartment = $request->input('id_department');
        $projectDTO->result = $request->input('result');
        $projectDTO->idYear = $request->input('id_year');
        $projectDTO->obstacle = $request->input('obstacle');
        $projectDTO->id = $request->input('id');
        $projectDTO->budget =  $request->input('budget');
        $projectDTO->idProject =  $request->input('id_project');

        // Action plan
        $actionPlanDTO = new ActionPlanDTO();
        $actionPlanDTO->actionPlanID = $request->input('id_actionplan');
        $projectDTO->actionPlanDTO = $actionPlanDTO;


        // indicator
        $indicators = $request->input('indicator_activity');
        foreach ($indicators as $key => $value) {
            $indicatorDTO = new IndicatorDTO();
            $indicatorDTO->indicatorName = $value['indicator_name'];
            $indicatorDTO->idUnit = $value['unit_name']['value'];
            $indicatorDTO->goal = $value['goal'];
            $projectDTO->indicatorsDTO[] = $indicatorDTO;
        }

        // ActivitiyDetailSpendMoney
        // $ActivityDetailSpend = $request->input('ActivitiyDetailSpendMoney');
        // foreach ($ActivityDetailSpend as $key => $value) {
        //     $ActivityDetailSpendDTO = new ActivitiyDetailSpendMoneyDTO();
        //     $ActivityDetailSpendDTO->detailName = $value['indicator_name'];
        //     $ActivityDetailSpendDTO->idUnit = $value['unit_name']['value'];
        //     // $ActivityDetailSpendDTO->goal = $value['goal'];
        //     $projectDTO->ActivitiyDetailSpendMoneyDTO[] = $ActivityDetailSpendDTO;
        // }

        //objective
        $objectives = $request->input('objective_activity');
        foreach ($objectives as $key => $value) {
            $objectiveDTO = new ObjectiveDTO();
            $objectiveDTO->objectiveName = $value['name'];
            $projectDTO->ObjectivesDTO[] = $objectiveDTO;
        }

        // employee
        $employees = $request->input('employee');
        foreach ($employees as $key => $value) {
            $employeeDTO = new EmployeeDTO();
            $employeeDTO->idUser = $value['id'];
            $employeeDTO->type = 1;
            if ($key == 0) {
                $employeeDTO->main = 1;
            } else {
                $employeeDTO->main = 0;
            }
            $projectDTO->employeesDTO[] = $employeeDTO;
        }

        // teacher
        $teachers = $request->input('teacher');
        foreach ($teachers as $key => $value) {
            $teacherDTO = new TeacherDTO();
            $teacherDTO->idUser = $value['id'];
            $teacherDTO->type = 2;
            if ($key == 0) {
                $teacherDTO->main = 1;
            } else {
                $teacherDTO->main = 0;
            }
            $projectDTO->teachersDTO[] = $teacherDTO;
        }


        // Okr detail project
        $okrDetailProjects = $request->input('okr_detail_activity');
        foreach ($okrDetailProjects as $key => $value) {
            $okrDetailProjectDTO = new OkrDetailProjectDTO();
            $okrDetailProjectDTO->idOkr = $value['id'];
            $projectDTO->okrDetailProjectsDTO[] = $okrDetailProjectDTO;
        }

        // Principles
        $principles = $request->input('activity_principle');
        foreach ($principles as $key => $value) {
            $principleDTO = new PrincipleDTO();
            $principleDTO->idPriciples = $value;
            $projectDTO->principlesDTO[] = $principleDTO;
        }


        // Style Activtiy Detail
        $styleActivtiyDetail = $request->input('style_activity_detail');
        foreach ($styleActivtiyDetail as $key => $value) {
            $styleActivtiyDetailDTO = new StyleActivtiyDetailDTO();
            $styleActivtiyDetailDTO->idStyle = $value;
            $projectDTO->styleActivtiyDetailsDTO[] = $styleActivtiyDetailDTO;
        }

        // Activtiy Detail
        $ActivtiyDetail = $request->input('ActivityDetailSpendMoney');
        foreach ($ActivtiyDetail as $key => $value) {
            $ActivitiySpendMoneyDTO = new ActivitySpendMoneyDTO();
            $ActivitiySpendMoneyDTO->name = $value['name_spendmoney'];
            $ActivitiySpendMoneyDTO->idUnit = $value['unit_name']['value'];

            $projectDTO->ActivitiySpendMoneyDTO[] = $ActivitiySpendMoneyDTO;
        }

        return $projectDTO;
    }


    public function projectRequestToProjectDTO(ProjectRequest $request)
    {
        $projectDTO = new ProjectDTO();

        $projectDTO->projectName = $request->input('project_name');
        // $projectDTO->agency = $request->input('agency');
        $projectDTO->abstract = $request->input('abstract');
        $projectDTO->timeStart = $request->input('time_start');
        $projectDTO->timeEnd = $request->input('time_end');
        $projectDTO->location = $request->input('location');

        $projectDTO->idDepartment = $request->input('id_department');
        $projectDTO->result = $request->input('result');
        $projectDTO->idYear = $request->input('id_year');
        $projectDTO->obstacle = $request->input('obstacle');
        $projectDTO->budget =  $request->input('budget');
        $projectDTO->projectNumber =  $request->input('project_number');


        // Action plan
        $actionPlanDTO = new ActionPlanDTO();
        $actionPlanDTO->actionPlanID = $request->input('id_actionplan');
        $projectDTO->actionPlanDTO = $actionPlanDTO;


        // project principle
        // $projectPrinciples = $request->input('project_principle');
        // $projectDTO->projectPrinciples =  $projectPrinciples;

        // indicator
        $indicators = $request->input('indicator');
        foreach ($indicators as $key => $value) {
            $indicatorDTO = new IndicatorDTO();
            $indicatorDTO->indicatorName = $value['indicator_name'];
            $indicatorDTO->idUnit = $value['unit_name']['value'];
            $indicatorDTO->goal = $value['goal'];
            $projectDTO->indicatorsDTO[] = $indicatorDTO;
        }

        // object
        $objectives = $request->input('objective');
        foreach ($objectives as $key => $value) {
            $objectiveDTO = new ObjectiveDTO();
            $objectiveDTO->objectiveName = $value['name'];
            $projectDTO->ObjectivesDTO[] = $objectiveDTO;
        }

        // employee
        $employees = $request->input('employee');
        foreach ($employees as $key => $value) {
            $employeeDTO = new EmployeeDTO();
            $employeeDTO->idUser = $value['id'];
            $employeeDTO->type = 1;
            if ($key == 0) {
                $employeeDTO->main = 1;
            } else {
                $employeeDTO->main = 0;
            }
            $projectDTO->employeesDTO[] = $employeeDTO;
        }

        // teacher
        $teachers = $request->input('teacher');
        foreach ($teachers as $key => $value) {
            $teacherDTO = new TeacherDTO();
            $teacherDTO->idUser = $value['id'];
            $teacherDTO->type = 2;
            if ($key == 0) {
                $teacherDTO->main = 1;
            } else {
                $teacherDTO->main = 0;
            }
            $projectDTO->teachersDTO[] = $teacherDTO;
        }

        // Style Details
        $styleDetails = $request->input('style_detail');
        foreach ($styleDetails as $key => $value) {
            $styleDetailDTO = new StyleDetailDTO();
            $styleDetailDTO->idStyle = $value;
            $projectDTO->styleDetailsDTO[] = $styleDetailDTO;
        }

        // Okr detail project
        $okrDetailProjects = $request->input('okr_detail_project', []);
        foreach ($okrDetailProjects as $item) {
            $okrDetailProjectDTO = new OkrDetailProjectDTO();
            $okrDetailProjectDTO->idOkr = $item['id']; // ใช้เฉพาะ id
            $projectDTO->okrDetailProjectsDTO[] = $okrDetailProjectDTO;
        }
        // Principles
        $principles = $request->input('project_principle', []);
        foreach ($principles as $key => $value) {
            $principleDTO = new PrincipleDTO();
            $principleDTO->namePriciples = $value;
            $projectDTO->principlesDTO[] = $principleDTO;
        }


        // Principles
        // $styleActivtiyDetail = $request->input('style_detail', []);
        // foreach ($styleActivtiyDetail as $key => $value) {
        //     $styleActivtiyDetailDTO = new StyleActivtiyDetailDTO();
        //     $styleActivtiyDetailDTO->idStyle = $value;
        //     $projectDTO->styleActivtiyDetailsDTO[] = $styleActivtiyDetailDTO;
        // }

        // // Result
        // $results = $request->input('result');
        // foreach ($results as $key => $value) {
        //     $resultDTO = new ResultDTO();
        //     $resultDTO->nameResult = $value;
        //     $projectDTO->resultsDTO[] = $resultDTO;
        // }

        // // Obstacle
        // $obstacles = $request->input('obstacle');
        // foreach ($obstacles as $key => $value) {
        //     $obstacleDTO = new ObstacleDTO();
        //     $obstacleDTO->nameObstacle = $value;
        //     $projectDTO->obstaclesDTO[] = $obstacleDTO;
        // }
        return $projectDTO;
    }

    public function projectEditRequestToProjectDTO(ProjectEditRequest $request, string $id)
    {
        $projectDTO = new ProjectDTO();

        $projectDTO->projectName = $request->input('project_name');
        $projectDTO->id = $id;
        $projectDTO->abstract = $request->input('abstract');
        $projectDTO->timeStart = $request->input('time_start');
        $projectDTO->timeEnd = $request->input('time_end');
        $projectDTO->location = $request->input('location');

        $projectDTO->idDepartment = $request->input('id_department');
        $projectDTO->result = $request->input('result');
        $projectDTO->idYear = $request->input('id_year');
        $projectDTO->obstacle = $request->input('obstacle');
        $projectDTO->budget =  $request->input('budget');
        $projectDTO->projectNumber =  $request->input('project_number');


        // Style Details
        $styleDetails = $request->input('style_detail');
        foreach ($styleDetails as $key => $value) {
            $styleDetailDTO = new StyleDetailDTO();
            $styleDetailDTO->idStyle = $value;
            $projectDTO->styleDetailsDTO[] = $styleDetailDTO;
        }

        $principles = $request->input('project_principle', []);
        foreach ($principles as $key => $value) {
            $principleDTO = new PrincipleDTO();
            $principleDTO->namePriciples = $value;
            $projectDTO->principlesDTO[] = $principleDTO;
        }

        return $projectDTO;
    }
    public function ActivityEditRequestToActivityDTO(ActivityEditRequest $request, string $id)
    {
        $projectDTO = new ActivityDTO();

        $projectDTO->nameActivity = $request->input('name_activity');
        $projectDTO->activityID = $id;
        $projectDTO->abstract = $request->input('abstract');
        $projectDTO->timeStart = $request->input('time_start');
        $projectDTO->timeEnd = $request->input('time_end');
        $projectDTO->location = $request->input('location');

        $projectDTO->idDepartment = $request->input('id_department');
        $projectDTO->result = $request->input('result');
        $projectDTO->idYear = $request->input('id_year');
        $projectDTO->obstacle = $request->input('obstacle');
        $projectDTO->budget =  $request->input('budget');
        $projectDTO->id =  $request->input('id');


        // Style Details
        $styleDetails = $request->input('activity_style_detail');
        foreach ($styleDetails as $key => $value) {
            $styleDetailDTO = new StyleDetailDTO();
            $styleDetailDTO->idStyle = $value;
            $projectDTO->styleDetailsDTO[] = $styleDetailDTO;
        }

        $principles = $request->input('activity_principle', []);
        foreach ($principles as $key => $value) {
            $principleDTO = new PrincipleDTO();
            $principleDTO->namePriciples = $value;
            $projectDTO->principlesDTO[] = $principleDTO;
        }

        return $projectDTO;
    }
    public function departmentRequestToDepertmentDTO(DepartmentRequest $request)
    {
        $departmentDTO = new DepartmentDTO();

        $departmentDTO->nameDepartment = $request->input('departments_name');
        return $departmentDTO;
    }

    public function typeRequestToTypeDTO(TypeRequest $request)
    {
        $typeDTO = new TypeDTO();

        $typeDTO->nameStyle = $request->input('style_name');
        return $typeDTO;
    }

    public function principleRequestToPrincipleDTO(PrincipleRequest $request)
    {
        $principleDTO = new PrincipleDTO();

        $principleDTO->namePriciples = $request->input('principle_name');
        return $principleDTO;
    }

    public function positionRequestToPositionDTO(PositionRequest $request)
    {
        $positionDTO = new PositionDTO();

        $positionDTO->namePosition = $request->input('position_name');
        return $positionDTO;
    }
    public function yearRequestToYearDTO(YearRequest $request)
    {
        $YearDTO = new YearDTO();

        $YearDTO->nameYear = $request->input('year');
        $YearDTO->budget = $request->input('budget');
        return $YearDTO;
    }

    public function strategicRequestToStrategicDTO(StrategicRequest $request)
    {
        $strategicDTO = new StrategicDTO();

        $strategicDTO->numberStrategic = $request->input('strategic_number');
        $strategicDTO->nameStrategic = $request->input('strategic_name');
        $strategicDTO->idYear = $request->input('id_year');
        $strategicDTO->budget = $request->input('budget');
        return $strategicDTO;
    }

    public function actionplanRequestToActionplanDTO(ActionplanRequest $request)
    {
        $strategicDTO = new ActionPlanDTO();

        $strategicDTO->actionPlanNumber = $request->input('action_plan_number');
        $strategicDTO->nameAp = $request->input('name_ap');
        $strategicDTO->idYear = $request->input('id_year');
        $strategicDTO->idStrategic = $request->input('id_strategic');
        $strategicDTO->budget = $request->input('budget');
        return $strategicDTO;
    }

    public function activityDetailRequestToActivityDetailDTO(ActivitydetailRequest $request)
    {
        $activityDetailDTO = new ActivityDetailDTO();

        $activityDetailDTO->detail = $request->input('detail');
        $activityDetailDTO->price = $request->input('price');
        $activityDetailDTO->start_date = $request->input('start_date');
        $activityDetailDTO->end_date = $request->input('end_date');
        $activityDetailDTO->station = $request->input('station');
        $activityDetailDTO->report_data = $request->input('report_data');
        $activityDetailDTO->id_employee = $request->input('id_employee');
        $activityDetailDTO->id_activity = $request->input('id_activity');
        $principles = $request->input('activitiydetailspendmoney', []);
        foreach ($principles as $key => $value) {
            $dataDTO = new ActivitiyDetailSpendMoneyDTO();
            $dataDTO->amount = $value['amount'];
            $dataDTO->price = $value['price'];
            $dataDTO->id_activity_spendmoney = $value['spendmoney_id'];
            $activityDetailDTO->ActivitiyDetailSpendMoneyDTO[] = $dataDTO;
        }
        return $activityDetailDTO;
    }

    public function storeFile($file, $path = "/uploads")
    {
        $originalExtension = strtolower($file->getClientOriginalExtension());
        // $originalName = strtolower($file->getClientOriginalName());
        // สร้างชื่อไฟล์ใหม่
        $fileName = Str::uuid()->toString() . '.' . $originalExtension;

        // จัดเก็บไฟล์ในโฟลเดอร์ public/uploads
        $filePath = $file->move(public_path($path), $fileName);

        return [
            'file_path'     => "/$path/" . $fileName,
            'absolute_path' => $filePath,
            'file_name'     => $fileName,
        ];
    }

    public function deleteFile($filePath)
    {
        // ตรวจสอบว่าไฟล์มีอยู่หรือไม่
        if (file_exists(public_path($filePath))) {
            // ลบไฟล์
            unlink(public_path($filePath));
            return [
                'status'    => 'success',
                'message'   => 'File deleted successfully.',
                'file_path' => $filePath,
            ];
        } else {
            return [
                'status'    => 'error',
                'message'   => 'File does not exist.',
                'file_path' => $filePath,
            ];
        }
    }

    public function userRequestToUserDTO(UserRequest $request)
    {
        $userDTO = new UserDTO();

        $userDTO->name = $request->input('name');
        $userDTO->email = $request->input('email', "");
        if ($request->filled('password')) {
            $userDTO->password = $request->input('password');
        }
        // $userDTO->role = $request->input('role');
        $userDTO->urlImg = $request->file('url_img');
        $userDTO->academicPosition = $request->input('academic_position');
        $userDTO->idPosition = $request->input('id_position');
        return $userDTO;
    }

    public function okrRequestToOkrDTO(OkrRequest $request)
    {
        $dataDTO = new OkrDTO();

        $dataDTO->okrnumber = $request->input('okr_number');
        $dataDTO->okrname = $request->input('okr_name');
        $dataDTO->goal = $request->input('goal');
        $dataDTO->result = $request->input('result');
        $dataDTO->reportdata = $request->input('report_data');
        $dataDTO->startdate = $request->input('start_date');
        $dataDTO->enddate = $request->input('end_date');
        $dataDTO->idunit = $request->input('id_unit');
        $dataDTO->idyear = $request->input('id_year');


        // employee
        $employees = $request->input('employee');
        foreach ($employees as $key => $value) {
            $employeeDTO = new EmployeeDTO();
            $employeeDTO->idUser = $value['id'];
            $employeeDTO->type = 1;
            if ($key == 0) {
                $employeeDTO->main = 1;
            } else {
                $employeeDTO->main = 0;
            }
            $dataDTO->employeesDTO[] = $employeeDTO;
        }

        // teacher
        $teachers = $request->input('teacher');
        foreach ($teachers as $key => $value) {
            $teacherDTO = new TeacherDTO();
            $teacherDTO->idUser = $value['id'];
            $teacherDTO->type = 2;
            if ($key == 0) {
                $teacherDTO->main = 1;
            } else {
                $teacherDTO->main = 0;
            }
            $dataDTO->teachersDTO[] = $teacherDTO;
        }
        return $dataDTO;
    }


    public function activitySpendMoneyRequestToActivitySpendDTO(ActivitySpendMoneyRequest $request)
    {
        $strategicDTO = new ActivitySpendMoneyDTO();

        $strategicDTO->activityId = $request->input('id_activity');
        $strategicDTO->name = $request->input('activity_spendmoney_name');
        $strategicDTO->idUnit = $request->input('id_unit');
        return $strategicDTO;
    }

    public function activityDetailSpendMoneyRequestToActivityDetaiSpendMoneylDTO(ActivityDetailSpendMoneyRequest $request)
    {
        $activityDetailDTO = new ActivitiyDetailSpendMoneyDTO();
        $activityDetailDTO->amount =  $request->input('amount');
        $activityDetailDTO->price =  $request->input('price');
        $activityDetailDTO->id_activity_spendmoney =  $request->input('id_activity_spendmoney');
        $activityDetailDTO->id_activity_detail =  $request->input('id_activity_detail');
        return $activityDetailDTO;
    }
}
