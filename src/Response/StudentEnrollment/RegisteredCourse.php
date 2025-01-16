<?php

declare(strict_types=1);

namespace Uisits\AitsApi\Response\StudentEnrollment;

use Spatie\LaravelData\Data;
use Uisits\AitsApi\Response\ValidCourseRegistrationLevel;
use Uisits\AitsApi\Response\ValidRegistrationStatus;

class RegisteredCourse extends Data
{
    public function __construct(
        public ValidRegistrationStatus $validRegistrationStatus,
        public ValidGradingMode $validGradingMode,
        public ValidCourseRegistrationLevel $validCourseRegistrationLevel,
        public CourseSection $courseSection,
    ) {}
}
