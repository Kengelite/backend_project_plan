<?php

namespace App\Trait;

use App\Dto\ActionPlanDTO;
use App\Http\Requests\Admin\Manage\ProjectRequest;

use App\Dto\ObstacleDTO;
use App\Dto\OkrDetailProjectDTO;
use App\Dto\PrincipleDTO;
use App\Dto\ProjectDTO;
use App\Dto\ResultDTO;
use App\Dto\StyleDetailDTO;

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
}
