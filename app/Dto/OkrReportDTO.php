<?php

namespace App\Dto;

class OkrReportDTO
{
    public ?string $reportOkrId = null;
    public string $idOkr;
    public string $idUser;
    public string $reportDate;
    public string $resultValue;
    public string $detailLink;
    public string $reportDetail;
}