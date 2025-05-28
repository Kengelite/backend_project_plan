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
use App\Dto\ObstacleDTO;
use App\Dto\OkrDetailProjectDTO;
use App\Dto\PrincipleDTO;
use App\Dto\ProjectDTO;
use App\Dto\DepartmentDTO;
use App\Dto\ResultDTO;
use App\Dto\TypeDTO;
use App\Dto\StyleDetailDTO;
use App\Dto\PositionDTO;
use App\Dto\YearDTO;
use App\Dto\StrategicDTO;
use App\Dto\ActivityDetailDTO;

use App\Models\Principle;

trait Utils
{
    public function projectRequestToProjectDTO(ProjectRequest $request)
    {
        $projectDTO = new ProjectDTO();

        $projectDTO->projectName = $request->input('project_name');
        $projectDTO->agency = $request->input('agency');
        $projectDTO->abstract = $request->input('abstract');
        $projectDTO->timeStart = $request->input('time_start');
        $projectDTO->timeEnd = $request->input('time_end');
        $projectDTO->location = $request->input('location');

        // Action plan
        $actionPlanDTO = new ActionPlanDTO();
        $actionPlanDTO->actionPlanID = $request->input('id_actionplan');
        $projectDTO->actionPlanDTO = $actionPlanDTO;

        // Style Details
        $styleDetails = $request->input('style_detail');
        foreach ($styleDetails as $key => $value) {
            $styleDetailDTO = new StyleDetailDTO();
            $styleDetailDTO->idStyle = $value;
            $projectDTO->styleDetailsDTO[] = $styleDetailDTO;
        }

        // Okr detail project
        $okrDetailProjects = $request->input('okr_detail_project');
        foreach ($okrDetailProjects as $key => $value) {
            $okrDetailProjectDTO = new OkrDetailProjectDTO();
            $okrDetailProjectDTO->idOkr = $value;
            $projectDTO->okrDetailProjectsDTO[] = $okrDetailProjectDTO;
        }

        // Principles
        $principles = $request->input('principles');
        foreach ($principles as $key => $value) {
            $principleDTO = new PrincipleDTO();
            $principleDTO->namePriciples = $value;
            $projectDTO->principlesDTO[] = $principleDTO;
        }

        // Result
        $results = $request->input('result');
        foreach ($results as $key => $value) {
            $resultDTO = new ResultDTO();
            $resultDTO->nameResult = $value;
            $projectDTO->resultsDTO[] = $resultDTO;
        }

        // Obstacle
        $obstacles = $request->input('obstacle');
        foreach ($obstacles as $key => $value) {
            $obstacleDTO = new ObstacleDTO();
            $obstacleDTO->nameObstacle = $value;
            $projectDTO->obstaclesDTO[] = $obstacleDTO;
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
        return $activityDetailDTO;

    }
}
