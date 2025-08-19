<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureStudentHsGpa;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\AzureStudentGpa\Gpa;

class AzureStudentHsGpa extends Data
{
    public function __construct(
        public ?string $ownerId,
        public ?string $userid,
        public ?string $sourceapplication,
        public ?HighSchool $validHighSchool,
        public ?string $gpa,
        public ?string $classSize,
        public ?string $classRank,
        public ?string $percentile,
        public ?string $graduationDate,
        public ?string $highSchoolSubject,
        public ?string $requestName,
        public ?string $diplomaName,
        public ?string $diplomaType,
    ) {}
}
