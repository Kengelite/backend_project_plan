<?php

namespace App\Dto;

class ActivityDTO extends ProjectDTO
{
    public string $activityID;
    public string $nameActivity;
    public string $id;
    public string $idStrategic;
    public string $idProject;

    /** @var StyleDetailDTO|null */
    public ?StyleDetailDTO $styleDetailDTO;

    /** @var StyleDetailDTO[]|null */
    public array $styleDetailsDTO;

    /** @var PrincipleDTO|null */
    public ?PrincipleDTO $principleDTO;

    /** @var PrincipleDTO[]|null */
    public array $principlesDTO;
}
