<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureBasicEmployee;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\AzureStudentGpa\Gpa;
use Uisits\AitsApi\Response\Campus;
use Uisits\AitsApi\Response\Person;

class AzureEmployee extends Data
{
    public function __construct(
        public ?string $status,
        public ?string $workPeriod,
        public ?string $userid,
        public ?string $timeStatus,
        public ?string $group,
        public ?string $sourceApplication,
        public ?string $flsa,
        public ?string $institutionalId,
        public ?Campus $validCampus,
        public ?HomeOrganization $homeOrganization,
        public ?DistributionOrganization $distributionOrganization,
        public ?KeyValue $validEmployeeClass,
        public ?KeyValue $validLeaveCategory,
        public ?KeyValue $validBenefitCategory,
        public ?EmploymentDates $employmentDates,
        public ?EmploymentLeave $employmentLeave,
        public ?EmploymentTermination $employmentTermination,
        public ?string $eVerifyCaseNumber,
        public ?string $eVerifyEffectiveDate,
        public ?KeyValue $homeCollege,
    ) {}
}
