<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Campus;
use Uisits\AitsApi\Response\College;
use Uisits\AitsApi\Response\Department;

class Course extends Data
{
    #[Computed]
    public ?string $course = null;

    public function __construct(
        public string $courseSubjectAbbreviation,
        public string $courseNumber,
        public Campus $validCampus,
        public string $courseTitle,
        public College $validCollege,
        public Department $validDepartment,
    ) {
        $this->course = $this->courseSubjectAbbreviation . ' ' . $this->courseNumber;
    }
}
