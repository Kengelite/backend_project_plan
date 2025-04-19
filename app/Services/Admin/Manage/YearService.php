<?php 

namespace App\Services\Admin\Manage;

use App\Dto\YearDTO;
use App\Models\Year;
use App\Trait\Utils;

class YearService
{
    use Utils;
    public function getAll()
    {
        $year = Year::orderByDesc('year')->paginate(10)->withQueryString();;
        return $year;
    }
}