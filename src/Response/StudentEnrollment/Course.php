<?php

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\Campus;
use Uisits\AitsApi\Response\College;
use Uisits\AitsApi\Response\Department;

class Course extends Data
{
    public function __construct(
        public string $courseSubjectAbbreviation,
        public string $courseNumber,
        public Campus $validCampus,
        public string $courseTitle,
        public College $validCollege,
        public Department $validDepartment,
    ) {}
}
