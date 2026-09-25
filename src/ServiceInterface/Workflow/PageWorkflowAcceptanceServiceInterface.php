<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Workflow;

use App\Paging\DTO\Workflow\PageWorkflowAcceptanceReportDTO;

interface PageWorkflowAcceptanceServiceInterface
{
    public function buildReport(): PageWorkflowAcceptanceReportDTO;
}
