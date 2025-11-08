<?php

namespace App\Dto;

class OkrDTO
{
    public string $okrId;
    public string $okrnumber;
    public string $okrname;
    public string $goal;
    public string $result;
    public ?string $reportdata;
    public string $startdate;
    public string $enddate;
    public string $idunit;
    public string $idyear;

    /** @var EmployeeDTO[]|null */
    public array $employeesDTO;

    /** @var TeacherDTO[]|null */
    public array $teachersDTO;
}
