<?php

namespace App\Dto;

class UserDTO
{
    public string $name;
    public ?string $email;
    public string $password;
    public string $role;
    public mixed $urlImg;
    public string $academicPosition;
    public string $idPosition;
}
