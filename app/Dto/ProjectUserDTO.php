<?php

namespace App\Dto;

class ProjectUserDTO
{
    public ?string $idProjectUser;
    public ?string $type;
    public ?string $main;
    public ?string $status;
    public ?string $idUser;
    public ?string $idProject;
    public ?string $idYear;

    public function __construct(
        ?string  $idProjectUser = null,
        ?string    $type = null,
        ?string   $main = null,
        ?string   $status = null,
        ?string   $idUser = null,
        ?string   $idProject = null,
        ?string   $idYear = null,
    ) {
        $this->idProjectUser = $idProjectUser;
        $this->type = $type;
        $this->main = $main;
        $this->status = $status;
        $this->idUser = $idUser;
        $this->idProject = $idProject;
        $this->idYear = $idYear;
    }
}
