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
}
