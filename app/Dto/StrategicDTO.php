<?php

namespace App\Dto;

use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Scalar\Float_;

class StrategicDTO
{
    public string $idStrategic;
    public string $numberStrategic;
    public string $nameStrategic;
    public float $budget;
    public string $status;
    public int $idYear;
}
