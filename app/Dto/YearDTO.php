<?php

namespace App\Dto;

use PhpParser\Node\Expr\Cast\Double;

class YearDTO
{
    public string $idYear;
    public string $nameYear;
    public string $status;
    public float $budget;
}
