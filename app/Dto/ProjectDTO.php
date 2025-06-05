<?php

namespace App\Dto;

class ProjectDTO
{
    public string $projectID;
    public string $projectNumber;
    public string $projectName;
    public string $budget;
    public string $spendMoney;
    public string $agency;
    public string $status;
    public string $statusPerformance;
    public string $statusReport;
    public string $detailShort;
    public string $abstract;
    public string $timeStart;
    public string $timeEnd;
    public string $location;
    public string $OKRID;
    public string $projectDetailID;

    public string $idDepartment;
    public string $result;
    public string $idYear;
    public string $obstacle;
    public array $projectPrinciples;

    /** @var StyleDetailDTO|null */
    public ?StyleDetailDTO $styleDetailDTO;

    /** @var StyleDetailDTO[]|null */
    public array $styleDetailsDTO;

    /** @var OkrDetailProjectDTO|null */
    public ?OkrDetailProjectDTO $okrDetailProjectDTO;

    /** @var OkrDetailProjectDTO[]|null */
    public array $okrDetailProjectsDTO;

    /** @var PrincipleDTO|null */
    public ?PrincipleDTO $principleDTO;

    /** @var PrincipleDTO[]|null */
    public array $principlesDTO;

    /** @var ResultDTO|null */
    public ?ResultDTO $resultDTO;

    /** @var ResultDTO[]|null */
    public array $resultsDTO;

    /** @var ObstacleDTO|null */
    public ?ObstacleDTO $obstacleDTO;

    /** @var ObstacleDTO[]|null */
    public array $obstaclesDTO;

    /** @var ActionPlanDTO|null */
    public ?ActionPlanDTO $actionPlanDTO;

    /** @var StyleActivtiyDetailDTO[]|null */
    public array $styleActivtiyDetailsDTO;

    /** @var ObjectiveDTO[]|null */
    public array $ObjectivesDTO;

    /** @var EmployeeDTO[]|null */
    public array $employeesDTO;

    /** @var TeacherDTO[]|null */
    public array $teachersDTO;

     /** @var IndicatorDTO[]|null */
    public array $indicatorsDTO;
}
