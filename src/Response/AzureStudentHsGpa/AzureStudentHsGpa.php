<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\AzureStudentHsGpa;

use Spatie\LaravelData\Data;

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
        /** @var SubjectCollection<int, Subject> */
        public ?SubjectCollection $highSchoolSubject,
        public ?string $requestName,
        public ?string $diplomaName,
        public ?string $diplomaType,
    ) {}
}
