<?php

declare(strict_types=1);

namespace App\Paging\ServiceInterface\Workflow;

use App\Paging\DTO\Workflow\PageWorkflowAcceptanceReport;

interface PageWorkflowAcceptanceServiceInterface
{
    public function buildReport(): PageWorkflowAcceptanceReport;
}
